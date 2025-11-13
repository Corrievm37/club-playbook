<x-app-layout>
    <div class="container mx-auto p-6 max-w-4xl">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">My Children</h1>
            <a href="{{ route('guardian.children.create') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Add Child
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border text-left">Name</th>
                    <th class="p-2 border text-left">DOB</th>
                    <th class="p-2 border text-left">School</th>
                    <th class="p-2 border text-left">Documents</th>
                    <th class="p-2 border">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($players as $p)
                    <tr>
                        <td class="p-2 border">{{ $p->last_name }}, {{ $p->first_name }}</td>
                        <td class="p-2 border">{{ $p->dob ?? '—' }}</td>
                        <td class="p-2 border">{{ $p->school_name ?? '—' }}</td>
                        <td class="p-2 border space-x-2">
                            @if(!empty($p->id_document_path))
                                <a href="{{ asset('storage/'.$p->id_document_path) }}" target="_blank" class="text-blue-700 underline">ID Doc</a>
                            @endif
                            @if(!empty($p->medical_aid_card_path))
                                <a href="{{ asset('storage/'.$p->medical_aid_card_path) }}" target="_blank" class="text-blue-700 underline">Medical Card</a>
                            @endif
                            @if(empty($p->id_document_path) && empty($p->medical_aid_card_path))
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="p-2 border text-center">
                            <a href="{{ route('guardian.children.edit', $p->id) }}" class="text-blue-700 underline">View / Update</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center">No linked children found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
