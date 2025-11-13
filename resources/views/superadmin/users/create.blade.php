<x-app-layout>
    <div class="container mx-auto p-6 max-w-2xl">
        <h1 class="text-2xl font-semibold mb-4">Create User</h1>
        <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-6" x-data="{ roles: @js(old('roles', [])) }">
            @csrf
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2" required />
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded p-2" required />
                @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" class="mt-1 w-full border rounded p-2" required />
                @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Assign Roles</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role }}" class="mr-2" @change="($event.target.checked ? roles.push('{{ $role }}') : roles = roles.filter(r => r !== '{{ $role }}'))" {{ in_array($role, old('roles', [])) ? 'checked' : '' }}>
                            <span class="text-sm">{{ ucwords(str_replace('_',' ', $role)) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div x-show="roles.includes('club_manager')" x-cloak>
                <label class="block text-sm font-medium">Designated Club (for Club Manager)</label>
                <select name="designated_club_id" class="mt-1 w-full border rounded p-2">
                    <option value="">— Select a Club —</option>
                    @foreach($clubs as $c)
                        <option value="{{ $c->id }}" {{ old('designated_club_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('designated_club_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="make_org_admin" value="1" class="mr-2" /> Make Super Admin
                </label>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
                <a href="{{ route('superadmin.users.index') }}" class="ml-2">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
