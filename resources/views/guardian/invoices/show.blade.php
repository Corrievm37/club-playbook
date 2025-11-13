@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
    @endif

    <h1 class="text-2xl font-semibold mb-4">Invoice {{ $invoice->number }}</h1>

    <div class="bg-white shadow rounded p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div><strong>Player:</strong> {{ optional($invoice->player)->first_name }} {{ optional($invoice->player)->last_name }}</div>
                <div><strong>Fee:</strong> {{ optional($invoice->fee)->name }}</div>
                <div><strong>Issue:</strong> {{ $invoice->issue_date }} | <strong>Due:</strong> {{ $invoice->due_date }}</div>
            </div>
            <div class="text-right">
                <div><strong>Total:</strong> ZAR {{ number_format($invoice->total_cents/100, 2) }}</div>
                @php($paidCents = (int) ($invoice->payments?->sum('amount_cents') ?? 0))
                @php(
                    $badge = (function() use ($invoice, $paidCents){
                        if ($invoice->status === 'paid') return ['PAID','bg-green-100 text-green-800'];
                        if ($paidCents > 0 && (int)$invoice->balance_cents > 0) return ['PARTIALLY PAID','bg-yellow-100 text-yellow-800'];
                        if ($invoice->status === 'overdue') return ['OVERDUE','bg-red-100 text-red-800'];
                        return [strtoupper($invoice->status),'bg-gray-100 text-gray-800'];
                    })()
                )
                <div><strong>Paid to date:</strong> ZAR {{ number_format($paidCents/100, 2) }}</div>
                <div><strong>Balance:</strong> ZAR {{ number_format($invoice->balance_cents/100, 2) }}</div>
                <div><strong>Status:</strong> <span class="inline-block px-2 py-0.5 rounded {{ $badge[1] }}">{{ $badge[0] }}</span></div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Payments</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="p-2 border text-left">Date</th>
                        <th class="p-2 border text-left">Method</th>
                        <th class="p-2 border text-right">Amount</th>
                        <th class="p-2 border text-left">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->payments as $p)
                    <tr>
                        <td class="p-2 border">{{ $p->paid_at }}</td>
                        <td class="p-2 border">{{ strtoupper($p->method) }}</td>
                        <td class="p-2 border text-right">{{ number_format($p->amount_cents/100, 2) }}</td>
                        <td class="p-2 border">{{ $p->reference }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="p-3 text-center text-gray-600" colspan="4">No payments recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h2 class="text-lg font-semibold mb-2">Proof of Payment</h2>
        @if($invoice->proof_path)
            <p class="mb-2">You have uploaded a proof of payment. You can view it here:</p>
            <a class="text-blue-700 underline" target="_blank" href="{{ asset('storage/'.$invoice->proof_path) }}">View uploaded proof</a>
            <p class="text-sm text-gray-600 mt-2">Status: {{ strtoupper($invoice->status) }}. A manager will review and confirm it.</p>
        @elseif($invoice->status !== 'paid')
            <form method="POST" action="{{ route('guardian.invoices.upload_proof', $invoice->id) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Upload Proof (PDF/JPG/PNG, max 8MB)</label>
                    <input type="file" name="proof" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 w-full border rounded p-2" required>
                    @error('proof')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Submit Proof</button>
            </form>
        @else
            <p class="text-sm text-gray-700">This invoice is fully paid.</p>
        @endif
    </div>

    <div class="mt-6">
        <a href="{{ route('guardian.invoices.index') }}" class="text-gray-700">Back to invoices</a>
    </div>
</div>
@endsection
