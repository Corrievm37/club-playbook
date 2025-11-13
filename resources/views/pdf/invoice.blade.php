<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; }
        .mb-2 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 24px; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f5f5f5; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-partial { background: #fef3c7; color: #92400e; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
        .badge-default { background: #e5e7eb; color: #111827; }
    </style>
</head>
<body>
    <div class="row mb-4">
        <div>
            <div style="display:flex; align-items:center; gap:10px;">
                @if(!empty($club->logo_url))
                    <img src="{{ storage_path('app/public/'.$club->logo_url) }}" alt="{{ $club->name }} logo" style="height:48px; width:auto;" />
                @endif
                <h2 style="margin:0;">{{ $club->name }}</h2>
            </div>
            <div>{{ $club->address_line1 }}</div>
            <div>{{ $club->city }} {{ $club->postal_code }}</div>
            <div>Tel: {{ $club->phone }}</div>
            <div>Email: {{ $club->email }}</div>
        </div>
        <div class="text-right">
            <h3 style="margin:0;">Invoice</h3>
            <div><strong>No:</strong> {{ $invoice->number }}</div>
            <div><strong>Date:</strong> {{ $invoice->issue_date }}</div>
            <div><strong>Due:</strong> {{ $invoice->due_date ?? '—' }}</div>
            @php($paidCents = (int) ($invoice->payments?->sum('amount_cents') ?? 0))
            @php($badgeClass = 'badge-default')
            @php($badgeText = strtoupper($invoice->status))
            @if($invoice->status === 'paid')
                @php($badgeClass = 'badge-paid')
                @php($badgeText = 'PAID')
            @elseif($paidCents > 0 && (int)$invoice->balance_cents > 0)
                @php($badgeClass = 'badge-partial')
                @php($badgeText = 'PARTIALLY PAID')
            @elseif($invoice->status === 'overdue')
                @php($badgeClass = 'badge-overdue')
                @php($badgeText = 'OVERDUE')
            @endif
            <div><strong>Status:</strong> <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span></div>
        </div>
    </div>

    <div class="mb-4">
        <strong>Billed To:</strong>
        <div>{{ optional($invoice->player)->first_name }} {{ optional($invoice->player)->last_name }}</div>
        <div>Age Group: {{ optional($invoice->player)->age_group }}</div>
    </div>

    <table class="mb-4">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount (ZAR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ optional($invoice->fee)->name ?? 'Club Fees' }}</td>
                <td class="text-right">{{ number_format(($invoice->subtotal_cents)/100, 2) }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td class="text-right">{{ number_format(($invoice->tax_cents)/100, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format(($invoice->total_cents)/100, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Paid to date</td>
                <td class="text-right">{{ number_format(($paidCents)/100, 2) }}</td>
            </tr>
            <tr>
                <td>Balance</td>
                <td class="text-right">{{ number_format(($invoice->balance_cents)/100, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mb-2"><strong>Payments</strong></div>
    <table class="mb-4">
        <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Reference</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->payments as $p)
            <tr>
                <td>{{ $p->paid_at }}</td>
                <td>{{ strtoupper($p->method) }}</td>
                <td>{{ $p->reference }}</td>
                <td class="text-right">{{ number_format(($p->amount_cents)/100, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-right">No payments recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mb-2"><strong>Payment Details</strong></div>
    <div>Account Name: {{ $club->bank_account_name }}</div>
    <div>Bank: {{ $club->bank_name }}</div>
    <div>Account Number: {{ $club->bank_account_number }}</div>
    <div>Branch Code: {{ $club->bank_branch_code }}</div>
    <div>Reference: {{ $invoice->number }}</div>
    @if($club->vat_number)
    <div>VAT Number: {{ $club->vat_number }}</div>
    @endif
</body>
</html>
