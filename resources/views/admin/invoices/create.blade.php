@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">New Invoice</h1>

    <form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Player</label>
                <select name="player_id" class="mt-1 w-full border rounded p-2" required>
                    @foreach($players as $p)
                        <option value="{{ $p->id }}">{{ $p->last_name }}, {{ $p->first_name }} ({{ $p->age_group }})</option>
                    @endforeach
                </select>
                @error('player_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Fee (optional)</label>
                <select name="fee_id" class="mt-1 w-full border rounded p-2">
                    <option value="">Select a fee</option>
                    @foreach($fees as $f)
                        <option value="{{ $f->id }}">{{ $f->name }} - ZAR {{ number_format($f->amount_cents/100, 2) }}</option>
                    @endforeach
                </select>
                @error('fee_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Issue Date</label>
                <input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" class="mt-1 w-full border rounded p-2" />
                @error('issue_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-1 w-full border rounded p-2" />
                @error('due_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Subtotal (ZAR cents)</label>
                <input type="number" name="subtotal_cents" value="{{ old('subtotal_cents') }}" class="mt-1 w-full border rounded p-2" />
                @error('subtotal_cents')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Tax (ZAR cents)</label>
                <input type="number" name="tax_cents" value="{{ old('tax_cents', 0) }}" class="mt-1 w-full border rounded p-2" />
                @error('tax_cents')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Invoice</button>
            <a href="{{ route('admin.invoices.index') }}" class="ml-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
