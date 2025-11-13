<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\ActiveClub;
use App\Models\Notice;
use App\Models\Membership;
use App\Models\Team;

class NoticeController extends Controller
{
    public function create()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $managedAge = null;
        if ($user->hasRole('team_manager')) {
            $mem = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'team_manager')->first();
            $managedAge = $mem?->managed_age_group;
        }
        $ageGroups = Team::where('club_id', $clubId)->pluck('age_group')->unique()->values();
        $roleOptions = ['guardian','coach','team_manager','club_manager','club_admin'];
        $noticesQuery = Notice::where('club_id', $clubId);
        if ($user->hasRole('team_manager')) {
            // Team managers only see their own notices in history
            $noticesQuery->where('created_by', $user->id);
        }
        $notices = $noticesQuery->orderByDesc('created_at')->limit(50)->get();
        return view('admin.notices.create', compact('managedAge','ageGroups','roleOptions','notices'));
    }

    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string',
            'age_group' => 'nullable|string', // null means ALL
            'audience_roles' => 'nullable|array',
            'audience_roles.*' => 'in:guardian,coach,team_manager,club_manager,club_admin',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Role scoping: team_manager limited to their managed_age_group and cannot broadcast to ALL (null)
        if ($user->hasRole('team_manager')) {
            $mem = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'team_manager')->first();
            abort_unless($mem && $mem->managed_age_group, 403);
            abort_if($data['age_group'] === null || $data['age_group'] === '', 403);
            abort_unless($data['age_group'] === $mem->managed_age_group, 403);
        }
        // club_manager/admin can set any age_group, including null for ALL

        // Enforce team_manager audience restriction: guardians only
        if ($user->hasRole('team_manager')) {
            $data['audience_roles'] = ['guardian'];
        }

        Notice::create([
            'club_id' => $clubId,
            'title' => $data['title'],
            'body' => $data['body'],
            'age_group' => $data['age_group'] ?: null,
            'created_by' => $user->id,
            'audience_roles' => $data['audience_roles'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);

        return redirect()->back()->with('status', 'Notice posted.');
    }

    public function edit(string $noticeId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $notice = Notice::where('club_id', $clubId)->findOrFail($noticeId);
        // Scope: team manager can edit only their age group and not ALL
        if ($user->hasRole('team_manager')) {
            $mem = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'team_manager')->first();
            abort_unless($mem && $mem->managed_age_group && $notice->age_group === $mem->managed_age_group, 403);
            // and only notices they created
            abort_unless((int)$notice->created_by === (int)$user->id, 403);
        }
        $managedAge = null;
        if ($user->hasRole('team_manager')) {
            $managedAge = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'team_manager')->value('managed_age_group');
        }
        $ageGroups = Team::where('club_id', $clubId)->pluck('age_group')->unique()->values();
        $roleOptions = ['guardian','coach','team_manager','club_manager','club_admin'];
        return view('admin.notices.edit', compact('notice','managedAge','ageGroups','roleOptions'));
    }

    public function update(Request $request, string $noticeId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $notice = Notice::where('club_id', $clubId)->findOrFail($noticeId);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'body' => 'required|string',
            'age_group' => 'nullable|string',
            'audience_roles' => 'nullable|array',
            'audience_roles.*' => 'in:guardian,coach,team_manager,club_manager,club_admin',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);
        if ($user->hasRole('team_manager')) {
            $mem = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'team_manager')->first();
            abort_unless($mem && $mem->managed_age_group, 403);
            abort_if($data['age_group'] === null || $data['age_group'] === '', 403);
            abort_unless($data['age_group'] === $mem->managed_age_group && $notice->age_group === $mem->managed_age_group, 403);
            // enforce guardians-only audience for team managers and only allow updating own notices
            $data['audience_roles'] = ['guardian'];
            abort_unless((int)$notice->created_by === (int)$user->id, 403);
        }
        $notice->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'age_group' => $data['age_group'] ?: null,
            'audience_roles' => $data['audience_roles'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
        ]);
        return redirect()->route('admin.notices.create')->with('status','Notice updated.');
    }

    public function destroy(string $noticeId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $notice = Notice::where('club_id', $clubId)->findOrFail($noticeId);
        if ($user->hasRole('team_manager')) {
            $memAge = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'team_manager')->value('managed_age_group');
            abort_unless($memAge && $notice->age_group === $memAge, 403);
            // only delete own notices
            abort_unless((int)$notice->created_by === (int)$user->id, 403);
        }
        $notice->delete();
        return redirect()->back()->with('status','Notice deleted.');
    }
}
