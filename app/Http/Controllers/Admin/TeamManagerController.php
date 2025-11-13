<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Membership;
use App\Models\Club;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeamManagerController extends Controller
{
    public function index()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        abort_unless(Auth::user()->hasAnyRole(['club_admin','club_manager','org_admin']), 403);

        $managers = Membership::with('user')
            ->where('club_id', $clubId)
            ->where('role', 'team_manager')
            ->orderByDesc('id')
            ->paginate(20);
        $club = Club::find($clubId);
        return view('admin.team_managers.index', compact('managers','club'));
    }

    public function create()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        abort_unless(Auth::user()->hasAnyRole(['club_admin','club_manager','org_admin']), 403);
        $club = Club::find($clubId);
        return view('admin.team_managers.create', compact('club'));
    }

    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        abort_unless(Auth::user()->hasAnyRole(['club_admin','club_manager','org_admin']), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'managed_age_group' => 'required|string|in:U6,U7,U8,U9,U10,U11,U12,U13,U14,U15,U16,U17,U18,U19',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        // Assign global role team_manager
        $user->assignRole('team_manager');
        // Create membership for this club as team_manager
        Membership::firstOrCreate(
            ['user_id' => $user->id, 'club_id' => $clubId],
            ['role' => 'team_manager', 'managed_age_group' => $data['managed_age_group']]
        );

        return redirect()->route('admin.team_managers.index')->with('status', 'Team Manager created.');
    }

    public function edit(Membership $membership)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        abort_unless(Auth::user()->hasAnyRole(['club_admin','club_manager','org_admin']), 403);
        abort_unless($membership->club_id == $clubId && $membership->role === 'team_manager', 404);
        $membership->load('user');
        return view('admin.team_managers.edit', [
            'membership' => $membership,
            'user' => $membership->user,
            'clubId' => $clubId,
        ]);
    }

    public function update(Request $request, Membership $membership)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        abort_unless(Auth::user()->hasAnyRole(['club_admin','club_manager','org_admin']), 403);
        abort_unless($membership->club_id == $clubId && $membership->role === 'team_manager', 404);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $membership->user_id,
            'password' => 'nullable|string|min:8',
            'managed_age_group' => 'required|string|in:U6,U7,U8,U9,U10,U11,U12,U13,U14,U15,U16,U17,U18,U19',
        ]);
        $user = $membership->user;
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        $membership->managed_age_group = $data['managed_age_group'];
        $membership->save();
        return redirect()->route('admin.team_managers.index')->with('status', 'Team Manager updated.');
    }
}
