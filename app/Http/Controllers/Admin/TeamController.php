<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Membership;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
        $teams = Team::where('club_id', $clubId)
            ->when($managedAge, fn($q) => $q->where('age_group', $managedAge))
            ->orderBy('age_group')
            ->orderBy('name')
            ->paginate(20);
        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
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
        return view('admin.teams.create', compact('managedAge'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'age_group' => 'required|string',
        ]);
        if ($user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            abort_unless($membership && $membership->managed_age_group === $data['age_group'], 403);
        }
        Team::create([
            'club_id' => $clubId,
            'age_group' => $data['age_group'],
            'name' => $data['name'],
            'season_year' => (int) now()->year,
        ]);
        return redirect()->route('admin.teams.index')->with('status', 'Team created.');
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
