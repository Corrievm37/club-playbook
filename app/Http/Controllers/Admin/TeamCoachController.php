<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\ActiveClub;
use App\Models\Team;
use App\Models\Membership;
use App\Models\User;

class TeamCoachController extends Controller
{
    public function create()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $teams = Team::where('club_id', $clubId)->orderBy('name')->get();
        $coachUserIds = Membership::where('club_id', $clubId)->where('role', 'coach')->pluck('user_id');
        $coaches = User::whereIn('id', $coachUserIds)->orderBy('name')->get();
        return view('admin.coaches.assign', compact('teams', 'coaches'));
    }

    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'user_id' => 'required|exists:users,id',
        ]);
        $team = Team::where('club_id', $clubId)->findOrFail($data['team_id']);
        // attach if not exists
        $team->coaches()->syncWithoutDetaching([$data['user_id']]);
        return redirect()->back()->with('status', 'Coach assigned to team.');
    }
}
