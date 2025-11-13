<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Teams</h1>
            <a href="{{ route('admin.teams.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Team</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">Age Group</th>
                        <th class="p-2 border text-left">Name</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teams as $team)
                    <tr>
                        <td class="p-2 border">{{ $team->age_group }}</td>
                        <td class="p-2 border">{{ $team->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-4 text-center">No teams yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $teams->links() }}</div>
    </div>
</x-app-layout>
