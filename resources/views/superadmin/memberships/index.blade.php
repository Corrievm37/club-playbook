<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <h1 class="text-2xl font-semibold mb-4">Memberships</h1>

        <form method="POST" action="{{ route('superadmin.memberships.store') }}" class="bg-white p-4 shadow rounded mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">User</label>
                <select name="user_id" class="mt-1 w-full border rounded p-2" required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                @error('user_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Club</label>
                <select name="club_id" class="mt-1 w-full border rounded p-2" required>
                    @foreach($clubs as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('club_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Role</label>
                <select name="role" class="mt-1 w-full border rounded p-2" required>
                    <option value="club_admin">Club Admin</option>
                    <option value="club_manager">Club Manager</option>
                    <option value="team_manager">Team Manager</option>
                    <option value="coach">Coach</option>
                    <option value="guardian">Guardian</option>
                </select>
                @error('role')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Assign</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">User</th>
                        <th class="p-2 border text-left">Club</th>
                        <th class="p-2 border text-left">Role</th>
                        <th class="p-2 border text-left">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberships as $m)
                    <tr>
                        <td class="p-2 border">{{ $m->user->name }} ({{ $m->user->email }})</td>
                        <td class="p-2 border">{{ $m->club->name }}</td>
                        <td class="p-2 border">{{ $m->role }}</td>
                        <td class="p-2 border">{{ $m->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center">No memberships found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $memberships->links() }}</div>
    </div>
</x-app-layout>
