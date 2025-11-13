<x-app-layout>
<div class="container mx-auto p-6">
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Fees @if($club) for {{ $club->name }} @endif</h1>
        <a href="{{ route('admin.fees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Fee</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Season</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border text-right">Amount (ZAR)</th>
                    <th class="p-2 border">Due Date</th>
                    <th class="p-2 border">Active</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fees as $fee)
                <tr>
                    <td class="p-2 border">{{ $fee->season_year }}</td>
                    <td class="p-2 border">{{ $fee->name }}</td>
                    <td class="p-2 border text-right">{{ number_format($fee->amount_cents/100, 2) }}</td>
                    <td class="p-2 border">{{ $fee->due_date }}</td>
                    <td class="p-2 border">{{ $fee->active ? 'Yes' : 'No' }}</td>
                    <td class="p-2 border space-x-2">
                        <a class="text-blue-600" href="{{ route('admin.fees.edit', $fee->id) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.fees.destroy', $fee->id) }}" class="inline" onsubmit="return confirm('Delete fee?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="p-4 text-center" colspan="6">No fees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $fees->links() }}</div>
</div>
</x-app-layout>
