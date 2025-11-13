@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Edit Fee</h1>

    <form method="POST" action="{{ route('admin.fees.update', $fee->id) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Season Year</label>
                <input type="number" name="season_year" value="{{ old('season_year', $fee->season_year) }}" class="mt-1 w-full border rounded p-2" required />
                @error('season_year')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', $fee->name) }}" class="mt-1 w-full border rounded p-2" required />
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Amount (ZAR cents)</label>
                <input type="number" name="amount_cents" value="{{ old('amount_cents', $fee->amount_cents) }}" class="mt-1 w-full border rounded p-2" required />
                @error('amount_cents')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', optional($fee->due_date)->format('Y-m-d')) }}" class="mt-1 w-full border rounded p-2" />
                @error('due_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="active" value="1" class="mr-2" {{ old('active', $fee->active) ? 'checked' : '' }} /> Active
                </label>
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold px-6 py-3 rounded shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 border border-blue-800">Save</button>
            <a href="{{ route('admin.fees.index') }}" class="ml-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
