<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Clubs</h1>
            <a href="{{ route('admin.clubs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Club</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">Name</th>
                        <th class="p-2 border text-left">Slug</th>
                        <th class="p-2 border text-left">Email</th>
                        <th class="p-2 border text-left">Phone</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clubs as $club)
                    <tr>
                        <td class="p-2 border">{{ $club->name }}</td>
                        <td class="p-2 border">{{ $club->slug }}</td>
                        <td class="p-2 border">{{ $club->email }}</td>
                        <td class="p-2 border">{{ $club->phone }}</td>
                        <td class="p-2 border space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.clubs.edit', $club->id) }}" class="text-blue-600 underline">Edit</a>
                            <a href="{{ url('/register/'.$club->slug.'/player') }}" target="_blank" class="text-green-700 underline">Registration Link</a>
                            <form method="POST" action="{{ route('admin.active-club.set') }}" class="inline">
                                @csrf
                                <input type="hidden" name="club_id" value="{{ $club->id }}" />
                                <button type="submit" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded text-xs">Set Active</button>
                            </form>
                            @role('org_admin')
                            <form method="POST" action="{{ route('superadmin.impersonate.start', $club->id) }}" class="inline" onsubmit="return confirm('Impersonate {{ $club->name }}?');">
                                @csrf
                                <button type="submit" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-2 py-1 rounded text-xs">Impersonate</button>
                            </form>
                            @endrole
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center">No clubs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $clubs->links() }}</div>
    </div>
</x-app-layout>
