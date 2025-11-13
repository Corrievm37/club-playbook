<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Club;
use App\Models\Membership;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('superadmin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name');
        $clubs = Club::orderBy('name')->get();
        return view('superadmin.users.create', compact('roles','clubs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
            'make_org_admin' => 'nullable|boolean',
            'designated_club_id' => 'nullable|integer|exists:clubs,id',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $selected = $data['roles'] ?? [];
        if ($request->boolean('make_org_admin') && !in_array('org_admin', $selected, true)) {
            $selected[] = 'org_admin';
        }
        if (!empty($selected)) {
            $user->syncRoles($selected);
        }
        // If creating a Club Manager and a designated club was chosen, create membership
        if (in_array('club_manager', $selected, true) && !empty($data['designated_club_id'])) {
            Membership::firstOrCreate(
                ['user_id' => $user->id, 'club_id' => (int)$data['designated_club_id']],
                ['role' => 'club_manager']
            );
        }
        return redirect()->route('superadmin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->pluck('name');
        $userRoles = $user->getRoleNames()->toArray();
        $clubs = Club::orderBy('name')->get();
        return view('superadmin.users.edit', compact('user','roles','userRoles','clubs'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
            'designated_club_id' => 'nullable|integer|exists:clubs,id',
            'designated_role' => 'nullable|string|in:club_admin,club_manager,team_manager,coach,guardian',
        ]);
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        $user->syncRoles($data['roles'] ?? []);
        // Upsert designated membership if provided
        if (!empty($data['designated_club_id']) && !empty($data['designated_role'])) {
            Membership::updateOrCreate(
                ['user_id' => $user->id, 'club_id' => (int)$data['designated_club_id']],
                ['role' => $data['designated_role']]
            );
        }
        return redirect()->route('superadmin.users.index')->with('status', 'User updated.');
    }
}
