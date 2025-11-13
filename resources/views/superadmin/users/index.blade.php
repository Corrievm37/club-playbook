<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Users</h1>
            <a href="{{ route('superadmin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New User</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">Name</th>
                        <th class="p-2 border text-left">Email</th>
                        <th class="p-2 border text-left">Roles</th>
                        <th class="p-2 border text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td class="p-2 border">{{ $u->name }}</td>
                        <td class="p-2 border">{{ $u->email }}</td>
                        <td class="p-2 border">{{ implode(', ', $u->getRoleNames()->toArray()) }}</td>
                        <td class="p-2 border">
                            <a href="{{ route('superadmin.users.edit', $u) }}" class="text-blue-700 underline">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $users->links() }}</div>
    </div>
</x-app-layout>
