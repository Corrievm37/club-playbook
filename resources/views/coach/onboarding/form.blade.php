<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-semibold">Coach Onboarding</h1>
        <p class="text-gray-600">{{ $invite->club->name ?? 'Club' }} — {{ $invite->category ? 'Category: '.$invite->category : '' }}</p>
    </div>

    <form method="POST" action="{{ route('coach.invite.submit', $invite->token) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">First name</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" class="mt-1 block w-full border rounded p-2" required>
            @error('first_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Last name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" class="mt-1 block w-full border rounded p-2" required>
            @error('last_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">South African ID Number</label>
            <input type="text" name="id_number" value="{{ old('id_number') }}" class="mt-1 block w-full border rounded p-2" required>
            @error('id_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">BokSmart Number (optional)</label>
            <input type="text" name="boksmart_number" value="{{ old('boksmart_number') }}" class="mt-1 block w-full border rounded p-2">
            @error('boksmart_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Password {{ Auth::check() ? '(you are already logged in)' : '' }}</label>
            <input type="password" name="password" class="mt-1 block w-full border rounded p-2" {{ Auth::check() ? 'disabled' : '' }}>
            @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Upload Qualifications (PDF/JPG/PNG) — multiple allowed</label>
            <input type="file" name="qualifications[]" multiple accept="application/pdf,image/*" class="mt-1 block w-full">
            @error('qualifications.*')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="pt-2">
            <x-primary-button class="w-full justify-center">Submit</x-primary-button>
        </div>
    </form>
</x-guest-layout>
