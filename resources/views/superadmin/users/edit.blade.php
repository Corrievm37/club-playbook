<x-app-layout>
    <div class="container mx-auto p-6 max-w-2xl">
        <h1 class="text-2xl font-semibold mb-4">Edit User</h1>
        <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="mt-1 w-full border rounded p-2" required />
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full border rounded p-2" required />
                @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Password (leave blank to keep)</label>
                <input type="password" name="password" class="mt-1 w-full border rounded p-2" />
                @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Assign Roles</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role }}" class="mr-2" {{ in_array($role, old('roles', $userRoles)) ? 'checked' : '' }}>
                            <span class="text-sm">{{ ucwords(str_replace('_',' ', $role)) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Designated Club</label>
                    <select name="designated_club_id" class="mt-1 w-full border rounded p-2">
                        <option value="">— None —</option>
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" {{ old('designated_club_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('designated_club_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Designated Role</label>
                    <select name="designated_role" class="mt-1 w-full border rounded p-2">
                        <option value="">— None —</option>
                        @foreach(['club_admin' => 'Club Admin','club_manager' => 'Club Manager','team_manager' => 'Team Manager','coach' => 'Coach','guardian' => 'Guardian'] as $val => $label)
                            <option value="{{ $val }}" {{ old('designated_role') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('designated_role')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                <a href="{{ route('superadmin.users.index') }}" class="ml-2">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
