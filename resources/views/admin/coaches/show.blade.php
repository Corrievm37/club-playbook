<x-app-layout>
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Coach Profile</h1>

    <div class="bg-white shadow rounded p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="text-sm text-gray-600">Name</div>
                <div class="font-medium">{{ $coach->name }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600">Email</div>
                <div class="font-medium">{{ $coach->email }}</div>
            </div>
            @if(!empty($coach->id_number))
            <div>
                <div class="text-sm text-gray-600">ID Number</div>
                <div class="font-medium">{{ $coach->id_number }}</div>
            </div>
            @endif
            @if(!empty($coach->boksmart_number))
            <div>
                <div class="text-sm text-gray-600">BokSmart Number</div>
                <div class="font-medium">{{ $coach->boksmart_number }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-3">Qualifications</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-3 py-2 text-left border">Title</th>
                        <th class="px-3 py-2 text-left border">File</th>
                        <th class="px-3 py-2 text-left border">Uploaded</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quals as $q)
                    <tr>
                        <td class="px-3 py-2 border">{{ $q->title ?? $q->original_name }}</td>
                        <td class="px-3 py-2 border">
                            <a href="{{ asset('storage/'.$q->file_path) }}" target="_blank" class="text-blue-700 hover:underline">View</a>
                        </td>
                        <td class="px-3 py-2 border">{{ $q->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-3 py-6 text-center text-gray-500">No qualifications uploaded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
