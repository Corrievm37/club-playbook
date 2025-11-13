<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\User;
use App\Models\Club;

class MembershipsController extends Controller
{
    public function index()
    {
        $memberships = Membership::with(['user','club'])->orderByDesc('id')->paginate(20);
        $users = User::orderBy('name')->get();
        $clubs = Club::orderBy('name')->get();
        return view('superadmin.memberships.index', compact('memberships','users','clubs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'club_id' => 'required|exists:clubs,id',
            'role' => 'required|in:club_admin,club_manager,coach,team_manager,guardian',
        ]);
        Membership::firstOrCreate([
            'user_id' => $data['user_id'],
            'club_id' => $data['club_id'],
        ], [
            'role' => $data['role'],
        ]);
        return redirect()->route('superadmin.memberships.index')->with('status', 'Membership saved.');
    }
}
