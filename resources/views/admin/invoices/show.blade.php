@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Invoice {{ $invoice->number }}</h1>
        <div class="space-x-2">
            <a href="{{ route('invoices.pdf', $invoice->id) }}" class="bg-green-700 text-white px-4 py-2 rounded">Download PDF</a>
            <form method="POST" action="{{ route('admin.invoices.destroy', $invoice->id) }}" class="inline" onsubmit="return confirm('Delete invoice?');">
                @csrf
                @method('DELETE')
                <button class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
            </form>
        </div>

    @if($invoice->proof_path)
    <div class="bg-white border rounded p-4 mb-6">
        <h3 class="text-lg font-semibold mb-2">Submitted Proof of Payment</h3>
        <p class="mb-2">A proof of payment was uploaded on {{ optional($invoice->proof_uploaded_at)->format('Y-m-d H:i') ?? 'N/A' }}.</p>
        <a href="{{ asset('storage/'.$invoice->proof_path) }}" target="_blank" class="text-blue-700 underline">View Proof</a>
        @if(in_array($invoice->status, ['pending','sent','overdue','partial']))
            <form method="POST" action="{{ route('admin.invoices.confirm_proof', $invoice->id) }}" class="mt-3" onsubmit="return confirm('Confirm payment and mark invoice as paid?');">
                @csrf
                <button class="bg-indigo-600 text-white px-4 py-2 rounded">Confirm Payment</button>
            </form>
        @endif
    </div>
    @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <div><strong>Player:</strong> {{ optional($invoice->player)->first_name }} {{ optional($invoice->player)->last_name }}</div>
            <div><strong>Fee:</strong> {{ optional($invoice->fee)->name }}</div>
            <div><strong>Issue:</strong> {{ $invoice->issue_date }} | <strong>Due:</strong> {{ $invoice->due_date }}</div>
            <div></div>
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

    <h2 class="text-xl font-semibold mb-2">Payments</h2>
    <div class="overflow-x-auto mb-4">
        <table class="min-w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">Date</th>
                    <th class="p-2 border">Method</th>
                    <th class="p-2 border text-right">Amount</th>
                    <th class="p-2 border">Reference</th>
                    <th class="p-2 border">Received By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->payments as $p)
                <tr>
                    <td class="p-2 border">{{ $p->paid_at }}</td>
                    <td class="p-2 border">{{ strtoupper($p->method) }}</td>
                    <td class="p-2 border text-right">{{ number_format($p->amount_cents/100, 2) }}</td>
                    <td class="p-2 border">{{ $p->reference }}</td>
                    <td class="p-2 border">
                        @php($receiver = is_numeric($p->received_by) ? optional(\App\Models\User::find($p->received_by))->name : $p->received_by)
                        {{ $receiver }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="p-4 text-center" colspan="5">No payments yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h3 class="text-lg font-semibold mb-2">Capture Payment</h3>
    <form method="POST" action="{{ route('admin.invoices.payments.store', $invoice->id) }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">Method</label>
            <select name="method" class="mt-1 w-full border rounded p-2">
                <option value="eft">EFT</option>
                <option value="cash">Cash</option>
                <option value="other">Other</option>
            </select>
            @error('method')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Amount (ZAR)</label>
            <input type="number" name="amount" step="0.01" min="0.01" class="mt-1 w-full border rounded p-2" required />
            @error('amount')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Paid At</label>
            <input type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}" class="mt-1 w-full border rounded p-2" required />
            @error('paid_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Reference</label>
            <input type="text" name="reference" class="mt-1 w-full border rounded p-2" />
            @error('reference')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-6">
            <label class="block text-sm font-medium">Note</label>
            <textarea name="note" class="mt-1 w-full border rounded p-2"></textarea>
            @error('note')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="md:col-span-6 flex items-center space-x-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Capture</button>
            <button type="button" class="px-3 py-2 rounded border" onclick="(function(){const i=document.querySelector('input[name=amount]'); if(i){ i.value='{{ number_format($invoice->balance_cents/100, 2, '.', '') }}'; }})();">Pay Full Balance</button>
            <a href="{{ route('admin.invoices.index') }}" class="ml-2">Back</a>
        </div>
    </form>
</div>
@endsection
