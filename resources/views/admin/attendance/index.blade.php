<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Attendance Sessions</h1>
            <a href="{{ route('admin.attendance.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Session</a>
        </div>

        @php
            $games = $sessions->getCollection()->filter(fn($x) => $x->type === 'game');
            $practices = $sessions->getCollection()->filter(fn($x) => $x->type === 'practice');
        @endphp

        <h2 class="text-xl font-semibold mt-2 mb-2">Games</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border mb-6">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">When</th>
                        <th class="p-2 border text-left">Age Group</th>
                        <th class="p-2 border text-left">Title</th>
                        <th class="p-2 border text-left">Location</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($games as $s)
                    <tr>
                        <td class="p-2 border">{{ $s->scheduled_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2 border">{{ $s->age_group }}</td>
                        <td class="p-2 border">{{ $s->title ?? '—' }}</td>
                        <td class="p-2 border">{{ $s->location ?? '—' }}</td>
                        <td class="p-2 border text-center">
                            <a class="text-blue-700 underline" href="{{ route('admin.attendance.show', $s->id) }}">Open Register</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center">No games yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="text-xl font-semibold mt-6 mb-2">Practices</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">When</th>
                        <th class="p-2 border text-left">Age Group</th>
                        <th class="p-2 border text-left">Title</th>
                        <th class="p-2 border text-left">Location</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($practices as $s)
                    <tr>
                        <td class="p-2 border">{{ $s->scheduled_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2 border">{{ $s->age_group }}</td>
                        <td class="p-2 border">{{ $s->title ?? '—' }}</td>
                        <td class="p-2 border">{{ $s->location ?? '—' }}</td>
                        <td class="p-2 border text-center">
                            <a class="text-blue-700 underline" href="{{ route('admin.attendance.show', $s->id) }}">Open Register</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center">No practices yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $sessions->links() }}</div>
    </div>
</x-app-layout>
