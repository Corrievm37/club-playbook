<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Team Managers — {{ $club->name }}</h1>
            <a href="{{ route('admin.team_managers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Add Team Manager</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">Name</th>
                        <th class="p-2 border text-left">Email</th>
                        <th class="p-2 border text-left">Managed Age Group</th>
                        <th class="p-2 border text-left">Assigned</th>
                        <th class="p-2 border text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($managers as $m)
                    <tr>
                        <td class="p-2 border">{{ $m->user->name }}</td>
                        <td class="p-2 border">{{ $m->user->email }}</td>
                        <td class="p-2 border">{{ $m->managed_age_group ?? '—' }}</td>
                        <td class="p-2 border">{{ $m->created_at->format('Y-m-d') }}</td>
                        <td class="p-2 border">
                            <a href="{{ route('admin.team_managers.edit', $m) }}" class="text-blue-700 underline">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center">No team managers yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $managers->links() }}</div>
    </div>
</x-app-layout>
