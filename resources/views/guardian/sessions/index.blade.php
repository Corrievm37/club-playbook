<x-app-layout>
    <div class="container mx-auto p-6 max-w-4xl">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif
        <h1 class="text-2xl font-semibold mb-4">Upcoming Sessions</h1>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border text-left">When</th>
                    <th class="p-2 border text-left">Type</th>
                    <th class="p-2 border text-left">Player</th>
                    <th class="p-2 border text-left">RSVP</th>
                    <th class="p-2 border text-left">Location</th>
                    <th class="p-2 border text-left">Notes</th>
                    <th class="p-2 border">Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($records as $rec)
                    <tr>
                        <td class="p-2 border">{{ $rec->session->scheduled_at->format('Y-m-d H:i') }}</td>
                        <td class="p-2 border">{{ ucfirst($rec->session->type) }}</td>
                        <td class="p-2 border">{{ $rec->player->last_name }}, {{ $rec->player->first_name }}</td>
                        <td class="p-2 border">
                            <form method="POST" action="{{ route('guardian.vote', $rec->id) }}" class="inline-flex items-center space-x-2">
                                @csrf
                                <select name="rsvp_status" class="border rounded p-1 text-sm">
                                    @foreach(['unknown'=>'Unknown','yes'=>'Yes','no'=>'No','maybe'=>'Maybe'] as $val=>$label)
                                        <option value="{{ $val }}" {{ $rec->rsvp_status===$val?'selected':'' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="px-2 py-1 text-xs bg-blue-600 text-white rounded">Save</button>
                            </form>
                        </td>
                        <td class="p-2 border">{{ $rec->session->location ?? '—' }}</td>
                        <td class="p-2 border">{{ $rec->session->notes ?? '—' }}</td>
                        <td class="p-2 border text-center">
                            <span class="text-gray-400">—</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center">No upcoming sessions.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
