<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Assign Teams — {{ $session->age_group }} — {{ $session->scheduled_at->format('Y-m-d H:i') }}</h1>
            <a href="{{ route('admin.attendance.show', $session->id) }}" class="text-blue-700 underline">Back to Register</a>
        </div>

        <div class="mb-4 text-sm text-gray-700">
            <div><strong>Type:</strong> {{ ucfirst($session->type) }}</div>
            <div><strong>Title:</strong> {{ $session->title ?? '—' }}</div>
            <div><strong>Location:</strong> {{ $session->location ?? '—' }}</div>
        </div>

        <form method="POST" action="{{ route('admin.attendance.assign.save', $session->id) }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border text-left">Player</th>
                            <th class="p-2 border text-left">Assign to Team</th>
                            <th class="p-2 border text-left">Jersey #</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($session->records as $rec)
                            <tr>
                                <td class="p-2 border">{{ $rec->player->last_name }}, {{ $rec->player->first_name }}</td>
                                <td class="p-2 border">
                                    <select name="assignments[{{ $rec->player->id }}]" class="border rounded p-1">
                                        <option value="">— None —</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ optional($assignments->get($rec->player->id))->team_id == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2 border w-32">
                                    <input type="number" min="1" max="23" name="jerseys[{{ $rec->player->id }}]" value="{{ optional($assignments->get($rec->player->id))->jersey_number }}" class="border rounded p-1 w-full" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Save Assignments</button>
            </div>
        </form>
    </div>
</x-app-layout>
