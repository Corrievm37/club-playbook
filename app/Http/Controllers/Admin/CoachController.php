<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\ActiveClub;
use App\Models\Membership;
use App\Models\User;
use App\Models\CoachQualification;

class CoachController extends Controller
{
    public function index()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $coachUserIds = Membership::where('club_id', $clubId)
            ->where('role', 'coach')
            ->pluck('user_id');

        $coaches = User::whereIn('id', $coachUserIds)
            ->orderBy('name')
            ->get();

        $qualsCounts = CoachQualification::selectRaw('user_id, COUNT(*) as cnt')
            ->where('club_id', $clubId)
            ->groupBy('user_id')
            ->pluck('cnt', 'user_id');

        return view('admin.coaches.index', compact('coaches', 'qualsCounts'));
    }

    public function show(User $user)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        // Ensure this user is a coach in the active club
        $isCoach = Membership::where('club_id', $clubId)
            ->where('role', 'coach')
            ->where('user_id', $user->id)
            ->exists();
        abort_unless($isCoach, 404);

        $quals = CoachQualification::where('user_id', $user->id)
            ->where('club_id', $clubId)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.coaches.show', [
            'coach' => $user,
            'quals' => $quals,
        ]);
    }
}
