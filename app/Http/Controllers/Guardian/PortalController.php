<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Guardian;
use App\Models\Player;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Membership;
use App\Models\Fee;
use App\Models\Invoice;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RsvpUpdatedPush;

class PortalController extends Controller
{
    public function children()
    {
        $user = Auth::user();
        // Guardians are linked via guardians table -> players
        $guards = Guardian::with('player')
            ->where(function($q) use ($user) {
                if (!empty($user->id)) {
                    $q->orWhere('user_id', $user->id);
                }
                if (!empty($user->email)) {
                    $q->orWhereRaw('LOWER(email) = ?', [mb_strtolower($user->email)]);
                }
            })
            ->get();

        // Auto-link fallback: if no guardian rows yet, link current user to any players they registered
        if ($guards->isEmpty() && !empty($user->id)) {
            $playersToLink = Player::where('registered_by_user_id', $user->id)->get();
            foreach ($playersToLink as $p) {
                Guardian::firstOrCreate(
                    [
                        'player_id' => $p->id,
                        'user_id' => $user->id,
                    ],
                    [
                        'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                        'last_name' => trim(str_replace(explode(' ', $user->name)[0] ?? '', '', $user->name)) ?: '',
                        'relationship' => 'guardian',
                        'email' => $user->email,
                        'primary_contact' => true,
                    ]
                );
            }
            // Reload after linking
            $guards = Guardian::with('player')
                ->where(function($q) use ($user) {
                    if (!empty($user->id)) { $q->orWhere('user_id', $user->id); }
                    if (!empty($user->email)) { $q->orWhereRaw('LOWER(email) = ?', [mb_strtolower($user->email)]); }
                })
                ->get();
        }
        $players = $guards->pluck('player')->filter();
        return view('guardian.children.index', compact('players'));
    }

    public function createChild()
    {
        // Requires active club
        abort_unless(ActiveClub::id(), 403);
        return view('guardian.children.create');
    }

    public function storeChild(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'sa_id_number' => 'nullable|string|max:30',
            'school_name' => 'nullable|string|max:255',
            'medical_aid_name' => 'nullable|string|max:255',
            'medical_aid_number' => 'nullable|string|max:255',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'medical_aid_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        $player = new Player();
        $player->club_id = $clubId;
        $player->first_name = $data['first_name'];
        $player->last_name = $data['last_name'];
        $player->dob = $data['dob'];
        $player->gender = $data['gender'] ?? null;
        $player->sa_id_number = $data['sa_id_number'] ?? null;
        $player->school_name = $data['school_name'] ?? null;
        $player->medical_aid_name = $data['medical_aid_name'] ?? null;
        $player->medical_aid_number = $data['medical_aid_number'] ?? null;
        $player->status = 'pending';
        if ($request->hasFile('id_document')) {
            $player->id_document_path = $request->file('id_document')->store('uploads/id_documents', 'public');
        }
        if ($request->hasFile('medical_aid_card')) {
            $player->medical_aid_card_path = $request->file('medical_aid_card')->store('uploads/medical_cards', 'public');
        }
        $player->registered_by_user_id = $user->id;
        $player->save();

        // Link guardian record for the current user
        Guardian::firstOrCreate(
            [
                'player_id' => $player->id,
                'email' => $user->email,
            ],
            [
                'user_id' => $user->id,
                'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                'last_name' => trim(str_replace(explode(' ', $user->name)[0] ?? '', '', $user->name)) ?: '',
                'relationship' => 'guardian',
                'primary_contact' => true,
            ]
        );

        // Auto-generate an initial invoice for registration for current season
        $fee = Fee::where('club_id', $clubId)
            ->where('season_year', (int) now()->year)
            ->orderByDesc('id')
            ->first();
        if ($fee) {
            $invoice = Invoice::create([
                'club_id' => $clubId,
                'player_id' => $player->id,
                'fee_id' => $fee->id,
                'subtotal_cents' => $fee->amount_cents,
                'tax_cents' => 0,
                'status' => 'sent',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->copy()->addDays(14)->toDateString(),
            ]);
            // Email primary guardian and CC others if any
            try {
                $primary = Guardian::where('player_id', $player->id)
                    ->where('primary_contact', true)->first();
                $others = Guardian::where('player_id', $player->id)
                    ->where(function($q){ $q->whereNull('primary_contact')->orWhere('primary_contact', false); })
                    ->whereNotNull('email')->get();
                if ($primary && $primary->email) {
                    $ccEmails = $others->pluck('email')->filter()->values()->all();
                    $mailable = new \App\Mail\InvoiceIssuedMail($invoice);
                    $mailer = \Mail::to([$primary->email => trim(($primary->first_name.' '.$primary->last_name)) ?: $primary->email]);
                    if (count($ccEmails) > 0) { $mailer->cc($ccEmails); }
                    $mailer->send($mailable);
                }
            } catch (\Throwable $e) {}
        }

        return redirect()->route('guardian.children')->with('status','Child added. Awaiting approval.');
    }

    public function editChild(string $playerId)
    {
        $player = Player::findOrFail($playerId);
        $this->authorizeGuardianFor($player);
        return view('guardian.children.edit', compact('player'));
    }

    public function updateChild(Request $request, string $playerId)
    {
        $player = Player::findOrFail($playerId);
        $this->authorizeGuardianFor($player);

        $data = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'school_name' => 'nullable|string|max:255',
            'medical_aid_name' => 'nullable|string|max:255',
            'medical_aid_number' => 'nullable|string|max:255',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'medical_aid_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('id_document')) {
            $player->id_document_path = $request->file('id_document')->store('uploads/id_documents', 'public');
        }
        if ($request->hasFile('medical_aid_card')) {
            $player->medical_aid_card_path = $request->file('medical_aid_card')->store('uploads/medical_cards', 'public');
        }

        // Only allow limited profile fields for guardian update
        if (isset($data['first_name'])) $player->first_name = $data['first_name'];
        if (isset($data['last_name'])) $player->last_name = $data['last_name'];
        if (array_key_exists('school_name', $data)) $player->school_name = $data['school_name'];
        if (array_key_exists('medical_aid_name', $data)) $player->medical_aid_name = $data['medical_aid_name'];
        if (array_key_exists('medical_aid_number', $data)) $player->medical_aid_number = $data['medical_aid_number'];
        $player->save();

        return redirect()->route('guardian.children')->with('status', 'Details updated.');
    }

    public function sessions()
    {
        $user = Auth::user();
        // find players for this guardian
        $guards = Guardian::with('player')
            ->where('email', $user->email)
            ->orWhere('user_id', $user->id ?? null)
            ->get();
        $playerIds = $guards->pluck('player.id')->filter()->all();
        $records = AttendanceRecord::with(['player','session'])
            ->whereIn('player_id', $playerIds)
            ->whereHas('session', function($q){
                $q->where('scheduled_at', '>=', now()->subDays(7));
            })
            ->orderByDesc('id')
            ->get();
        return view('guardian.sessions.index', compact('records'));
    }

    public function vote(Request $request, string $recordId)
    {
        $record = AttendanceRecord::with(['player','session'])->findOrFail($recordId);
        $this->authorizeGuardianFor($record->player);
        $data = $request->validate([
            'rsvp_status' => 'required|in:unknown,yes,no,maybe',
        ]);
        $record->rsvp_status = $data['rsvp_status'];
        $record->save();
        // Notify team managers responsible for this age group (web push)
        try {
            $clubId = $record->session->club_id;
            $age = $record->session->age_group;
            $managerUsers = Membership::with('user')
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->where('managed_age_group', $age)
                ->get()
                ->pluck('user')
                ->filter();
            if ($managerUsers->count() > 0) {
                Notification::send($managerUsers, new RsvpUpdatedPush($record));
            }
        } catch (\Throwable $e) {}
        return back()->with('status', 'RSVP updated.');
    }

    protected function authorizeGuardianFor(Player $player)
    {
        $user = Auth::user();
        $isGuardian = Guardian::where('player_id', $player->id)
            ->where(function($q) use ($user){
                $q->where('email', $user->email);
                if ($user->id) $q->orWhere('user_id', $user->id);
            })
            ->exists();
        abort_unless($isGuardian, 403);
    }
}
