<x-app-layout>
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Coaches</h1>
    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Name</th>
                    <th class="px-3 py-2 text-left">Email</th>
                    <th class="px-3 py-2 text-left">BokSmart</th>
                    <th class="px-3 py-2 text-right">Qualifications</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($coaches as $c)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $c->name }}</td>
                    <td class="px-3 py-2">{{ $c->email }}</td>
                    <td class="px-3 py-2">{{ $c->boksmart_number }}</td>
                    <td class="px-3 py-2 text-right">{{ (int)($qualsCounts[$c->id] ?? 0) }}</td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('admin.coaches.show', $c->id) }}" class="text-blue-700 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-3 py-6 text-center text-gray-500">No coaches yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
