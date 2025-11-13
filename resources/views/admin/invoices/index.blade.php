@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Invoices</h1>
        <a href="{{ route('admin.invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">New Invoice</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Number</th>
                    <th class="p-2 border">Player</th>
                    <th class="p-2 border">Fee</th>
                    <th class="p-2 border">Issue</th>
                    <th class="p-2 border">Due</th>
                    <th class="p-2 border text-right">Total</th>
                    <th class="p-2 border text-right">Balance</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td class="p-2 border">{{ $inv->number }}</td>
                    <td class="p-2 border">{{ optional($inv->player)->first_name }} {{ optional($inv->player)->last_name }}</td>
                    <td class="p-2 border">{{ optional($inv->fee)->name }}</td>
                    <td class="p-2 border">{{ $inv->issue_date }}</td>
                    <td class="p-2 border">{{ $inv->due_date }}</td>
                    <td class="p-2 border text-right">{{ number_format($inv->total_cents/100, 2) }}</td>
                    <td class="p-2 border text-right">{{ number_format($inv->balance_cents/100, 2) }}</td>
                    <td class="p-2 border">{{ strtoupper($inv->status) }}</td>
                    <td class="p-2 border space-x-2">
                        <a class="text-blue-600" href="{{ route('admin.invoices.show', $inv->id) }}">View</a>
                        <a class="text-green-700" href="{{ route('invoices.pdf', $inv->id) }}">PDF</a>
                        <form method="POST" action="{{ route('admin.invoices.destroy', $inv->id) }}" class="inline" onsubmit="return confirm('Delete invoice?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="p-4 text-center" colspan="9">No invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection
