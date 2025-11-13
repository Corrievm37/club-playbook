<x-app-layout>
    <div class="container mx-auto p-6">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Players ({{ ucfirst($status) }}) @if($club) - {{ $club->name }} @endif</h1>
            <div class="space-x-2">
                <a class="px-3 py-2 border rounded {{ $status==='pending'?'bg-gray-200':'' }}" href="{{ route('admin.players.index', ['status' => 'pending']) }}">Pending</a>
                <a class="px-3 py-2 border rounded {{ $status==='approved'?'bg-gray-200':'' }}" href="{{ route('admin.players.index', ['status' => 'approved']) }}">Approved</a>
                <a class="px-3 py-2 border rounded {{ $status==='rejected'?'bg-gray-200':'' }}" href="{{ route('admin.players.index', ['status' => 'rejected']) }}">Rejected</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border text-left">Name</th>
                        <th class="p-2 border text-left">DOB</th>
                        <th class="p-2 border text-left">Age Group</th>
                        <th class="p-2 border text-left">Season</th>
                        <th class="p-2 border text-left">Shirt Size</th>
                        <th class="p-2 border text-left">Shirt Handed Out</th>
                        <th class="p-2 border text-left">Documents</th>
                        <th class="p-2 border text-left">Status</th>
                        <th class="p-2 border text-left">Rejection Reason</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($players as $player)
                    <tr x-data="{ openReject:false, openViewReason:false }">
                        <td class="p-2 border">{{ $player->last_name }}, {{ $player->first_name }}</td>
                        <td class="p-2 border">{{ $player->dob }}</td>
                        <td class="p-2 border">{{ $player->age_group }}</td>
                        <td class="p-2 border">{{ $player->season_year }}</td>
                        <td class="p-2 border">{{ $player->shirt_size ?? '—' }}</td>
                        <td class="p-2 border">
                            @if($player->shirt_handed_out)
                                <span class="inline-block text-green-800 bg-green-100 px-2 py-0.5 rounded text-xs">Yes</span>
                            @else
                                <span class="inline-block text-gray-700 bg-gray-100 px-2 py-0.5 rounded text-xs">No</span>
                            @endif
                        </td>
                        <td class="p-2 border space-x-2">
                            @if(!empty($player->id_document_path))
                                <a href="{{ asset('storage/'.$player->id_document_path) }}" target="_blank" class="text-blue-700 underline">ID Doc</a>
                            @endif
                            @if(!empty($player->medical_aid_card_path))
                                <a href="{{ asset('storage/'.$player->medical_aid_card_path) }}" target="_blank" class="text-blue-700 underline">Medical Card</a>
                            @endif
                            @if(empty($player->id_document_path) && empty($player->medical_aid_card_path))
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="p-2 border">{{ ucfirst($player->status) }}</td>
                        <td class="p-2 border">
                            @if($player->status==='rejected' && $player->rejection_reason)
                                <button type="button" class="text-blue-700 underline" @click="openViewReason=!openViewReason">View</button>
                                <div x-show="openViewReason" x-cloak class="mt-2 border rounded p-2 bg-blue-50 text-sm whitespace-pre-wrap">
                                    {{ $player->rejection_reason }}
                                    <div class="text-right mt-1">
                                        <button type="button" class="text-xs underline" @click="openViewReason=false">Close</button>
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="p-2 border">
                            @if($player->status==='pending' || $player->status==='rejected')
                                <form method="POST" action="{{ route('admin.players.approve', $player->id) }}" class="inline">
                                    @csrf
                                    <button class="bg-green-700 hover:bg-green-800 text-white font-bold px-4 py-2 rounded shadow focus:outline-none focus:ring-2 focus:ring-green-300" onclick="return confirm('Approve this player?')">Approve</button>
                                </form>
                            @endif

                            @if($player->status==='pending' || $player->status==='approved')
                                <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-1.5 rounded shadow mt-2" @click="openReject=!openReject">Reject</button>
                                <div x-show="openReject" x-cloak class="mt-2 border rounded p-2 bg-red-50">
                                    <form method="POST" action="{{ route('admin.players.reject', $player->id) }}" class="space-y-2">
                                        @csrf
                                        <textarea name="reason" class="w-full border rounded p-2 text-sm" rows="3" placeholder="Reason (required)" required></textarea>
                                        <div class="flex items-center justify-end space-x-2">
                                            <button type="button" class="px-3 py-1.5 rounded border" @click="openReject=false">Cancel</button>
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-1.5 rounded shadow">Confirm Reject</button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if(!$player->shirt_handed_out)
                                <form method="POST" action="{{ route('admin.players.shirt_handed_out', $player->id) }}" class="inline">
                                    @csrf
                                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-3 py-1.5 rounded shadow mt-2" onclick="return confirm('Mark shirt as handed out to this player?')">Mark Shirt Handed Out</button>
                                </form>
                            @else
                                <span class="ml-2 text-xs text-green-700">Shirt handed out</span>
                            @endif

                            <form method="POST" action="{{ route('admin.invoices.regenerate_for_player', $player->id) }}" class="inline">
                                @csrf
                                <button class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-3 py-1.5 rounded shadow mt-2" onclick="return confirm('Generate or refresh the registration invoice for this player?')">Generate Registration Invoice</button>
                            </form>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-4 text-center">No players found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $players->links() }}</div>
    </div>
</x-app-layout>
