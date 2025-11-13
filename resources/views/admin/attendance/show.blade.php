<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">{{ ucfirst($session->type) }} Register — {{ $session->age_group }} — {{ $session->scheduled_at->format('Y-m-d H:i') }}</h1>
            <div class="space-x-3">
                @if($session->type === 'game')
                    <a href="{{ route('admin.attendance.assign', $session->id) }}" class="bg-indigo-600 text-white px-3 py-2 rounded">Assign Teams</a>
                    <a href="{{ route('admin.attendance.print', $session->id) }}" target="_blank" class="bg-gray-700 text-white px-3 py-2 rounded">Print Team Sheet</a>
                @endif
                <a href="{{ route('admin.attendance.index') }}" class="text-blue-700 underline">Back to Sessions</a>
            </div>
        </div>

        <div class="mb-4 text-sm text-gray-700">
            <div><strong>Title:</strong> {{ $session->title ?? '—' }}</div>
            <div><strong>Location:</strong> {{ $session->location ?? '—' }}</div>
            <div><strong>Notes:</strong> {{ $session->notes ?? '—' }}</div>
        </div>

        @if($session->type === 'game')
            @foreach($teams as $team)
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold">Team: {{ $team->name }}</h2>
                    <a href="{{ route('admin.attendance.team.print', [$session->id, $team->id]) }}" target="_blank" class="text-sm bg-gray-700 text-white px-3 py-1 rounded">Print</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border text-left">Player</th>
                                <th class="p-2 border text-left">RSVP</th>
                                <th class="p-2 border text-left">Present</th>
                                <th class="p-2 border text-left">Jersey #</th>
                                <th class="p-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $hasRows = false; @endphp
                            @foreach($session->records as $rec)
                                @php $a = $assignments[$rec->player->id] ?? null; @endphp
                                @if($a && $a->team_id === $team->id)
                                    @php $hasRows = true; @endphp
                                    <tr>
                                        <td class="p-2 border">{{ $rec->player->last_name }}, {{ $rec->player->first_name }}</td>
                                        <td class="p-2 border">
                                            <form method="POST" action="{{ route('admin.attendance.rsvp.update', [$session->id, $rec->id]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="rsvp_status" class="border rounded p-1 text-sm">
                                                    @foreach(['unknown'=>'Unknown','yes'=>'Yes','no'=>'No','maybe'=>'Maybe'] as $val=>$label)
                                                        <option value="{{ $val }}" {{ $rec->rsvp_status===$val?'selected':'' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="ml-2 px-2 py-1 text-xs bg-blue-600 text-white rounded">Save</button>
                                            </form>
                                        </td>
                                        <td class="p-2 border">
                                            <form method="POST" action="{{ route('admin.attendance.presence.update', [$session->id, $rec->id]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="present" value="0" />
                                                <label class="inline-flex items-center space-x-2">
                                                    <input type="checkbox" name="present" value="1" {{ $rec->present ? 'checked' : '' }} onchange="this.form.submit()" />
                                                    <span>Present</span>
                                                </label>
                                            </form>
                                        </td>
                                        <td class="p-2 border">{{ $a && $a->jersey_number ? $a->jersey_number : '—' }}</td>
                                        <td class="p-2 border text-center">
                                            <span class="text-gray-400">—</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$hasRows)
                                <tr>
                                    <td colspan="5" class="p-4 text-center">No players assigned to this team.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-2">Unassigned</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border text-left">Player</th>
                                <th class="p-2 border text-left">RSVP</th>
                                <th class="p-2 border text-left">Present</th>
                                <th class="p-2 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $hasUnassigned = false; @endphp
                            @foreach($session->records as $rec)
                                @php $a = $assignments[$rec->player->id] ?? null; @endphp
                                @if(!$a || !$a->team_id)
                                    @php $hasUnassigned = true; @endphp
                                    <tr>
                                        <td class="p-2 border">{{ $rec->player->last_name }}, {{ $rec->player->first_name }}</td>
                                        <td class="p-2 border">
                                            <form method="POST" action="{{ route('admin.attendance.rsvp.update', [$session->id, $rec->id]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="rsvp_status" class="border rounded p-1 text-sm">
                                                    @foreach(['unknown'=>'Unknown','yes'=>'Yes','no'=>'No','maybe'=>'Maybe'] as $val=>$label)
                                                        <option value="{{ $val }}" {{ $rec->rsvp_status===$val?'selected':'' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="ml-2 px-2 py-1 text-xs bg-blue-600 text-white rounded">Save</button>
                                            </form>
                                        </td>
                                        <td class="p-2 border">
                                            <form method="POST" action="{{ route('admin.attendance.presence.update', [$session->id, $rec->id]) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="present" value="0" />
                                                <label class="inline-flex items-center space-x-2">
                                                    <input type="checkbox" name="present" value="1" {{ $rec->present ? 'checked' : '' }} onchange="this.form.submit()" />
                                                    <span>Present</span>
                                                </label>
                                            </form>
                                        </td>
                                        <td class="p-2 border text-center"><span class="text-gray-400">—</span></td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!$hasUnassigned)
                                <tr>
                                    <td colspan="4" class="p-4 text-center">No unassigned players.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border text-left">Player</th>
                            <th class="p-2 border text-left">RSVP</th>
                            <th class="p-2 border text-left">Present</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($session->records as $rec)
                        <tr>
                            <td class="p-2 border">{{ $rec->player->last_name }}, {{ $rec->player->first_name }}</td>
                            <td class="p-2 border">
                                <form method="POST" action="{{ route('admin.attendance.rsvp.update', [$session->id, $rec->id]) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <select name="rsvp_status" class="border rounded p-1 text-sm">
                                        @foreach(['unknown'=>'Unknown','yes'=>'Yes','no'=>'No','maybe'=>'Maybe'] as $val=>$label)
                                            <option value="{{ $val }}" {{ $rec->rsvp_status===$val?'selected':'' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button class="ml-2 px-2 py-1 text-xs bg-blue-600 text-white rounded">Save</button>
                                </form>
                            </td>
                            <td class="p-2 border">
                                <form method="POST" action="{{ route('admin.attendance.presence.update', [$session->id, $rec->id]) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="present" value="0" />
                                    <label class="inline-flex items-center space-x-2">
                                        <input type="checkbox" name="present" value="1" {{ $rec->present ? 'checked' : '' }} onchange="this.form.submit()" />
                                        <span>Present</span>
                                    </label>
                                </form>
                            </td>
                            <td class="p-2 border text-center"><span class="text-gray-400">—</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center">No players found for this age group.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
