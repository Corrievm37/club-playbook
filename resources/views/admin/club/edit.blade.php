<x-app-layout>
<div class="container mx-auto p-6">
    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
    @endif

    <h1 class="text-2xl font-semibold mb-4">Club Settings</h1>

    <form method="POST" action="{{ route('admin.clubs.update', $club->id) }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" value="{{ old('name', $club->name) }}" class="mt-1 w-full border rounded p-2" required />
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

        <h2 class="text-xl font-semibold mt-6">Branding</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div>
                <label class="block text-sm font-medium">Club Logo</label>
                <input type="file" name="logo" accept="image/*" class="mt-1 w-full border rounded p-2" />
                @error('logo')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                <p class="text-xs text-gray-600 mt-1">PNG/JPG, up to 5MB. Shown on invoices, team sheets, and the site header.</p>
            </div>
            <div class="flex items-center gap-4">
                @if($club->logo_url)
                    <img src="{{ asset('storage/'.$club->logo_url) }}" alt="{{ $club->name }} logo" class="h-16 w-auto border rounded bg-white p-1" />
                @else
                    <div class="text-gray-500 text-sm">No logo uploaded yet.</div>
                @endif
            </div>
        </div>
            <div>
                <label class="block text-sm font-medium">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $club->slug) }}" class="mt-1 w-full border rounded p-2" required />
                @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $club->email) }}" class="mt-1 w-full border rounded p-2" />
                @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $club->phone) }}" class="mt-1 w-full border rounded p-2" />
                @error('phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Address Line 1</label>
                <input type="text" name="address_line1" value="{{ old('address_line1', $club->address_line1) }}" class="mt-1 w-full border rounded p-2" />
                @error('address_line1')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Address Line 2</label>
                <input type="text" name="address_line2" value="{{ old('address_line2', $club->address_line2) }}" class="mt-1 w-full border rounded p-2" />
                @error('address_line2')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">City</label>
                <input type="text" name="city" value="{{ old('city', $club->city) }}" class="mt-1 w-full border rounded p-2" />
                @error('city')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Province</label>
                <input type="text" name="province" value="{{ old('province', $club->province) }}" class="mt-1 w-full border rounded p-2" />
                @error('province')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Postal Code</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', $club->postal_code) }}" class="mt-1 w-full border rounded p-2" />
                @error('postal_code')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Country</label>
                <input type="text" name="country" value="{{ old('country', $club->country) }}" class="mt-1 w-full border rounded p-2" />
                @error('country')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
        </div>

        <h2 class="text-xl font-semibold mt-6">Bank Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Account Name</label>
                <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $club->bank_account_name) }}" class="mt-1 w-full border rounded p-2" />
                @error('bank_account_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Bank Name</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $club->bank_name) }}" class="mt-1 w-full border rounded p-2" />
                @error('bank_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Account Number</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $club->bank_account_number) }}" class="mt-1 w-full border rounded p-2" />
                @error('bank_account_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Branch Code</label>
                <input type="text" name="bank_branch_code" value="{{ old('bank_branch_code', $club->bank_branch_code) }}" class="mt-1 w-full border rounded p-2" />
                @error('bank_branch_code')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">VAT Number (optional)</label>
                <input type="text" name="vat_number" value="{{ old('vat_number', $club->vat_number) }}" class="mt-1 w-full border rounded p-2" />
                @error('vat_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold px-6 py-3 rounded shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-400 border border-blue-800">Save Settings</button>
        </div>
    </form>
</div>
</x-app-layout>
