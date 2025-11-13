<x-app-layout>
    <div class="container mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">New Fee @if($club) for {{ $club->name }} @endif</h1>

    <form method="POST" action="{{ route('admin.fees.store') }}" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Season Year</label>
                <input type="number" name="season_year" value="{{ old('season_year', now()->year) }}" class="mt-1 w-full border rounded p-2" required />
                @error('season_year')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2" required />
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Amount (ZAR cents)</label>
                <input type="number" name="amount_cents" value="{{ old('amount_cents', 0) }}" class="mt-1 w-full border rounded p-2" required />
                @error('amount_cents')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" class="mt-1 w-full border rounded p-2" />
                @error('due_date')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="active" value="1" class="mr-2" {{ old('active', true) ? 'checked' : '' }} /> Active
                </label>
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Fee</button>
            <a href="{{ route('admin.fees.index') }}" class="ml-2">Cancel</a>
        </div>
    </form>
    </div>
</x-app-layout>
