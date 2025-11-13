<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\Membership;
use App\Models\Player;
use App\Models\Team;
use App\Models\TeamAssignment;
use App\Models\Guardian;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewSessionCreatedMail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewSessionCreatedPush;

class AttendanceController extends Controller
{
    public function index()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $managedAge = null;
        if ($user) {
            if ($user->hasRole('team_manager')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'team_manager')
                    ->first();
                if ($membership && $membership->managed_age_group) {
                    $managedAge = $membership->managed_age_group;
                }
            } elseif ($user->hasRole('coach')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'coach')
                    ->first();
                if ($membership && $membership->coach_category) {
                    $managedAge = $membership->coach_category;
                }
            }
        }

        $sessions = AttendanceSession::where('club_id', $clubId)
            ->when($managedAge, fn($q) => $q->where('age_group', $managedAge))
            ->orderByDesc('scheduled_at')
            ->paginate(20);
        return view('admin.attendance.index', compact('sessions'));
    }

    public function create()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $managedAge = null;
        if ($user && $user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            $managedAge = $membership?->managed_age_group;
        }
        return view('admin.attendance.create', compact('managedAge'));
    }

    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();

        $data = $request->validate([
            'type' => 'required|in:practice,game',
            'title' => 'nullable|string|max:255',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'age_group' => 'required|string',
        ]);

        // Enforce team manager age group
        if ($user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            abort_unless($membership && $membership->managed_age_group === $data['age_group'], 403);
        }

        $session = AttendanceSession::create([
            'club_id' => $clubId,
            'age_group' => $data['age_group'],
            'type' => $data['type'],
            'title' => $data['title'] ?? null,
            'scheduled_at' => $data['scheduled_at'],
            'location' => $data['location'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        // Pre-create records for all players in age group for quick register
        $players = Player::where('club_id', $clubId)
            ->where('age_group', $session->age_group)
            ->orderBy('last_name')
            ->get();
        foreach ($players as $p) {
            AttendanceRecord::firstOrCreate([
                'attendance_session_id' => $session->id,
                'player_id' => $p->id,
            ]);
        }

        // Notify guardians via email for this age group
        try {
            $playerIds = Player::where('club_id', $clubId)
                ->where('age_group', $session->age_group)
                ->pluck('id');
            if ($playerIds->count() > 0) {
                $emails = Guardian::whereIn('player_id', $playerIds)
                    ->where('primary_contact', true)
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->unique()
                    ->values()
                    ->all();
                foreach ($emails as $email) {
                    Mail::to($email)->queue(new NewSessionCreatedMail($session));
                }
                // Web push to guardians who have accounts and subscriptions
                $guardianUsers = Guardian::whereIn('player_id', $playerIds)
                    ->where('primary_contact', true)
                    ->whereNotNull('user_id')
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter();
                if ($guardianUsers->count() > 0) {
                    Notification::send($guardianUsers, new NewSessionCreatedPush($session));
                }
            }
        } catch (\Throwable $e) {
            // swallow email errors, do not block flow
        }

        return redirect()->route('admin.attendance.show', $session->id)->with('status', 'Session created.');
    }

    public function show(string $id)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $session = AttendanceSession::with(['records.player'])->where('club_id', $clubId)->findOrFail($id);
        $user = Auth::user();
        if ($user) {
            if ($user->hasRole('team_manager')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'team_manager')
                    ->first();
                if ($membership && $membership->managed_age_group) {
                    abort_unless($session->age_group === $membership->managed_age_group, 403);
                }
            } elseif ($user->hasRole('coach')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'coach')
                    ->first();
                if ($membership && $membership->coach_category) {
                    abort_unless($session->age_group === $membership->coach_category, 403);
                }
            }
        }
        // load current team assignments for display (team name and jersey number)
        $assignments = TeamAssignment::with('team')
            ->where('attendance_session_id', $session->id)
            ->get()
            ->keyBy('player_id');
        // load teams for this age group (for per-team registers)
        $teams = Team::where('club_id', $clubId)
            ->where('age_group', $session->age_group)
            ->orderBy('name')
            ->get();
        return view('admin.attendance.show', compact('session', 'assignments', 'teams'));
    }

    public function assign(string $id)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $session = AttendanceSession::with(['records.player'])->where('club_id', $clubId)->findOrFail($id);
        // Only for game sessions
        abort_unless($session->type === 'game', 404);
        $user = Auth::user();
        if ($user) {
            if ($user->hasRole('team_manager')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'team_manager')
                    ->first();
                if ($membership && $membership->managed_age_group) {
                    abort_unless($session->age_group === $membership->managed_age_group, 403);
                }
            } elseif ($user->hasRole('coach')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'coach')
                    ->first();
                if ($membership && $membership->coach_category) {
                    abort_unless($session->age_group === $membership->coach_category, 403);
                }
            }
        }
        $teams = Team::where('club_id', $clubId)->where('age_group', $session->age_group)->orderBy('name')->get();
        // Existing assignments map
        $assignments = TeamAssignment::where('attendance_session_id', $session->id)->get()->keyBy('player_id');
        return view('admin.attendance.assign', compact('session','teams','assignments'));
    }

    public function saveAssignments(\Illuminate\Http\Request $request, string $id)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $session = AttendanceSession::where('club_id', $clubId)->findOrFail($id);
        abort_unless($session->type === 'game', 404);
        $user = Auth::user();
        if ($user) {
            if ($user->hasRole('team_manager')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'team_manager')
                    ->first();
                if ($membership && $membership->managed_age_group) {
                    abort_unless($session->age_group === $membership->managed_age_group, 403);
                }
            } elseif ($user->hasRole('coach')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'coach')
                    ->first();
                if ($membership && $membership->coach_category) {
                    abort_unless($session->age_group === $membership->coach_category, 403);
                }
            }
        }

        $data = $request->validate([
            'assignments' => 'array',
            'assignments.*' => 'nullable|integer',
            'jerseys' => 'array',
            'jerseys.*' => 'nullable|integer|min:1|max:23',
        ]);
        $map = $data['assignments'] ?? [];
        $jerseys = $data['jerseys'] ?? [];

        // Validate teams belong to club and age group
        $validTeamIds = Team::where('club_id', $clubId)->where('age_group', $session->age_group)->pluck('id')->all();
        $validTeamSet = array_flip($validTeamIds);

        // Enforce per-team uniqueness of jersey numbers within this session
        $used = []; // [teamId][jersey] => playerId
        $errors = [];
        foreach ($map as $playerId => $teamId) {
            if ($teamId === null || $teamId === '' ) {
                continue; // unassigned is fine
            }
            if (!isset($validTeamSet[(int)$teamId])) {
                continue; // invalid team will be skipped later
            }
            $jerseyNumber = isset($jerseys[$playerId]) && $jerseys[$playerId] !== '' ? (int)$jerseys[$playerId] : null;
            if ($jerseyNumber !== null) {
                if (!isset($used[(int)$teamId])) $used[(int)$teamId] = [];
                if (isset($used[(int)$teamId][$jerseyNumber])) {
                    $errors["jerseys.$playerId"] = "Jersey number already used for this team in this game.";
                } else {
                    $used[(int)$teamId][$jerseyNumber] = $playerId;
                }
            }
        }
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        foreach ($map as $playerId => $teamId) {
            $jerseyNumber = isset($jerseys[$playerId]) && $jerseys[$playerId] !== '' ? (int)$jerseys[$playerId] : null;
            if ($teamId === null || $teamId === '' ) {
                // If no team selected, remove assignment fully
                TeamAssignment::where('attendance_session_id', $session->id)->where('player_id', $playerId)->delete();
                continue;
            }
            if (!isset($validTeamSet[(int)$teamId])) {
                continue; // skip invalid team
            }
            // Ensure player is part of session's age group and club
            $player = Player::where('club_id', $clubId)->where('age_group', $session->age_group)->find($playerId);
            if (!$player) continue;
            // Final guard: ensure no existing conflicting jersey for the same team
            if ($jerseyNumber !== null) {
                $conflict = TeamAssignment::where('attendance_session_id', $session->id)
                    ->where('team_id', (int)$teamId)
                    ->where('jersey_number', $jerseyNumber)
                    ->where('player_id', '!=', $player->id)
                    ->exists();
                if ($conflict) {
                    return back()->withErrors(["jerseys.$playerId" => 'Jersey number already used for this team in this game.'])->withInput();
                }
            }
            TeamAssignment::updateOrCreate(
                ['attendance_session_id' => $session->id, 'player_id' => $player->id],
                ['team_id' => (int)$teamId, 'jersey_number' => $jerseyNumber]
            );
        }
        return redirect()->route('admin.attendance.assign', $session->id)->with('status', 'Assignments saved.');
    }

    public function updateRsvp(Request $request, string $sessionId, string $recordId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $data = $request->validate([
            'rsvp_status' => 'required|in:unknown,yes,no,maybe',
        ]);
        $session = AttendanceSession::where('club_id', $clubId)->findOrFail($sessionId);
        $record = AttendanceRecord::with('player')->where('attendance_session_id', $session->id)->findOrFail($recordId);

        // Enforce team manager/coach scoping
        $user = Auth::user();
        if ($user) {
            if ($user->hasRole('team_manager')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'team_manager')
                    ->first();
                if ($membership && $membership->managed_age_group) {
                    abort_unless($session->age_group === $membership->managed_age_group, 403);
                }
            } elseif ($user->hasRole('coach')) {
                $membership = Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->where('role', 'coach')
                    ->first();
                if ($membership && $membership->coach_category) {
                    abort_unless($session->age_group === $membership->coach_category, 403);
                }
            }
        }

        $record->rsvp_status = $data['rsvp_status'];
        $record->save();
        return back()->with('status', 'RSVP updated.');
    }

    public function updatePresence(Request $request, string $sessionId, string $recordId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $data = $request->validate([
            'present' => 'required|boolean',
        ]);
        $session = AttendanceSession::where('club_id', $clubId)->findOrFail($sessionId);
        $record = AttendanceRecord::with('player')->where('attendance_session_id', $session->id)->findOrFail($recordId);

        // Enforce team manager scoping
        $user = Auth::user();
        if ($user && $user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                abort_unless($session->age_group === $membership->managed_age_group, 403);
            }
        }

        $record->present = (bool) $data['present'];
        $record->save();
        return back()->with('status', 'Presence updated.');
    }

    public function printTeams(string $id)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $session = AttendanceSession::with(['records.player'])->where('club_id', $clubId)->findOrFail($id);
        abort_unless($session->type === 'game', 404);

        // Enforce team manager scoping
        $user = Auth::user();
        if ($user && $user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                abort_unless($session->age_group === $membership->managed_age_group, 403);
            }
        }

        $teams = Team::where('club_id', $clubId)
            ->where('age_group', $session->age_group)
            ->orderBy('name')
            ->get();

        $assignments = TeamAssignment::with(['team','player'])
            ->where('attendance_session_id', $session->id)
            ->get();

        // Group players by team id; include unassigned
        $grouped = [
            'unassigned' => [],
        ];
        foreach ($teams as $t) {
            $grouped[$t->id] = [];
        }
        foreach ($assignments as $a) {
            if ($a->team_id && isset($grouped[$a->team_id])) {
                $grouped[$a->team_id][] = $a;
            } else {
                $grouped['unassigned'][] = $a;
            }
        }

        // Sort players in each team by jersey number ascending, then last name
        foreach ($grouped as $key => &$list) {
            usort($list, function($x, $y) {
                $jnx = $x->jersey_number ?? PHP_INT_MAX;
                $jny = $y->jersey_number ?? PHP_INT_MAX;
                if ($jnx === $jny) {
                    return strcmp($x->player->last_name, $y->player->last_name);
                }
                return $jnx <=> $jny;
            });
        }

        return view('admin.attendance.print', [
            'session' => $session,
            'teams' => $teams,
            'grouped' => $grouped,
        ]);
    }

    public function printTeam(string $sessionId, string $teamId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $session = AttendanceSession::where('club_id', $clubId)->findOrFail($sessionId);
        abort_unless($session->type === 'game', 404);

        // Enforce team manager scoping
        $user = Auth::user();
        if ($user && $user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                abort_unless($session->age_group === $membership->managed_age_group, 403);
            }
        }

        $team = Team::where('club_id', $clubId)
            ->where('age_group', $session->age_group)
            ->findOrFail($teamId);

        $assignments = TeamAssignment::with(['player'])
            ->where('attendance_session_id', $session->id)
            ->where('team_id', $team->id)
            ->get()
            ->sort(function($a, $b) {
                $jnx = $a->jersey_number ?? PHP_INT_MAX;
                $jny = $b->jersey_number ?? PHP_INT_MAX;
                if ($jnx === $jny) {
                    return strcmp($a->player->last_name, $b->player->last_name);
                }
                return $jnx <=> $jny;
            });

        return view('admin.attendance.print_team', [
            'session' => $session,
            'team' => $team,
            'assignments' => $assignments,
        ]);
    }
}
