@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">My Invoices</h1>
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
    @endif
    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50 text-left">
                    <th class="p-3">Invoice</th>
                    <th class="p-3">Player</th>
                    <th class="p-3">Amount</th>
                    <th class="p-3">Balance</th>
                    <th class="p-3">Due</th>
                    <th class="p-3">Status</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr class="border-b">
                    <td class="p-3">{{ $inv->number }}</td>
                    <td class="p-3">{{ optional($inv->player)->first_name }} {{ optional($inv->player)->last_name }}</td>
                    <td class="p-3">ZAR {{ number_format($inv->total_cents/100, 2) }}</td>
                    <td class="p-3">ZAR {{ number_format($inv->balance_cents/100, 2) }}</td>
                    <td class="p-3">{{ $inv->due_date }}</td>
                    <td class="p-3">{{ strtoupper($inv->status) }}</td>
                    <td class="p-3">
                        <a href="{{ route('guardian.invoices.show', $inv->id) }}" class="text-blue-700">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="p-4 text-center text-gray-600" colspan="6">No invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection
