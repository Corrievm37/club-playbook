<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Club;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Mail;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clubId = ActiveClub::id();
        $club = $clubId ? Club::find($clubId) : null;
        $status = request('status', 'pending');
        $user = Auth::user();
        $managedAge = null;
        if ($user && $clubId && $user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                $managedAge = $membership->managed_age_group;
            }
        }
        $players = Player::when($clubId, fn($q) => $q->where('club_id', $clubId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($managedAge, fn($q) => $q->where('age_group', $managedAge))
            ->orderBy('last_name')
            ->paginate(20);
        return view('admin.players.index', compact('players','status','club'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not used
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not used
    }

    public function approve(string $playerId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $player = Player::where('club_id',$clubId)->findOrFail($playerId);
        $player->status = 'approved';
        $player->approved_by_user_id = auth()->id();
        $player->approved_at = now();
        $player->rejection_reason = null;
        $player->save();
        // Notify guardians
        $emails = Guardian::where('player_id', $player->id)->whereNotNull('email')->pluck('email')->all();
        if (!empty($emails)) {
            $club = Club::find($clubId);
            foreach ($emails as $email) {
                Mail::raw(
                    "Dear Guardian,\n\n".
                    "Your player {$player->first_name} {$player->last_name} has been APPROVED for {$club->name}.\n\n".
                    "Regards, {$club->name}",
                    function ($message) use ($email, $club, $player) {
                        $message->to($email)
                            ->subject("Player Approved: {$player->first_name} {$player->last_name} - {$club->name}");
                    }
                );
            }
        }
        return redirect()->route('admin.players.index', ['status' => 'pending'])->with('status', 'Player approved.');
    }

    public function reject(\Illuminate\Http\Request $request, string $playerId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $player = Player::where('club_id',$clubId)->findOrFail($playerId);
        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        $player->status = 'rejected';
        $player->approved_by_user_id = auth()->id();
        $player->approved_at = now();
        $player->rejection_reason = $data['reason'];
        $player->save();
        // Notify guardians
        $emails = Guardian::where('player_id', $player->id)->whereNotNull('email')->pluck('email')->all();
        if (!empty($emails)) {
            $club = Club::find($clubId);
            foreach ($emails as $email) {
                Mail::raw(
                    "Dear Guardian,\n\n".
                    "Your player {$player->first_name} {$player->last_name} has been REJECTED for {$club->name}.\n".
                    "Reason: {$player->rejection_reason}\n\n".
                    "Regards, {$club->name}",
                    function ($message) use ($email, $club, $player) {
                        $message->to($email)
                            ->subject("Player Rejected: {$player->first_name} {$player->last_name} - {$club->name}");
                    }
                );
            }
        }
        return back()->with('status', 'Player rejected.');
    }

    public function markShirtHandedOut(\Illuminate\Http\Request $request, string $playerId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        abort_unless($user && ($user->hasRole('club_admin') || $user->hasRole('club_manager') || $user->hasRole('team_manager')), 403);

        $player = Player::where('club_id', $clubId)->findOrFail($playerId);

        // If team manager, enforce managed age group
        if ($user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                abort_unless($player->age_group === $membership->managed_age_group, 403);
            }
        }

        $player->shirt_handed_out = true;
        $player->save();

        return back()->with('status', 'Shirt marked as handed out.');
    }
}
