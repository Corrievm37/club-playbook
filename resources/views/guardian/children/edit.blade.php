<x-app-layout>
    <div class="container mx-auto p-6 max-w-3xl">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif
        <h1 class="text-2xl font-semibold mb-4">View / Update Child</h1>

        <div class="mb-4 text-sm text-gray-700">
            <div><strong>Name:</strong> {{ $player->first_name }} {{ $player->last_name }}</div>
            <div><strong>DOB:</strong> {{ $player->dob ?? '—' }}</div>
            <div><strong>School:</strong> {{ $player->school_name ?? '—' }}</div>
        </div>

        <form action="{{ route('guardian.children.update', $player->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $player->first_name) }}" class="mt-1 w-full border rounded p-2" />
                    @error('first_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $player->last_name) }}" class="mt-1 w-full border rounded p-2" />
                    @error('last_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium">School Name</label>
                    <input type="text" name="school_name" value="{{ old('school_name', $player->school_name) }}" class="mt-1 w-full border rounded p-2" />
                    @error('school_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Medical Aid Name</label>
                    <input type="text" name="medical_aid_name" value="{{ old('medical_aid_name', $player->medical_aid_name) }}" class="mt-1 w-full border rounded p-2" />
                    @error('medical_aid_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Medical Aid Number</label>
                    <input type="text" name="medical_aid_number" value="{{ old('medical_aid_number', $player->medical_aid_number) }}" class="mt-1 w-full border rounded p-2" />
                    @error('medical_aid_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <h2 class="text-xl font-semibold">Documents</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Player ID Document (PDF or Photo)</label>
                    @if($player->id_document_path)
                        <div class="mb-2 text-sm"><a href="{{ asset('storage/'.$player->id_document_path) }}" target="_blank" class="text-blue-700 underline">View current</a></div>
                    @endif
                    <input type="file" name="id_document" accept="image/*,application/pdf" capture="environment" class="mt-1 w-full border rounded p-2" />
                    @error('id_document')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Medical Aid Card (PDF or Photo)</label>
                    @if($player->medical_aid_card_path)
                        <div class="mb-2 text-sm"><a href="{{ asset('storage/'.$player->medical_aid_card_path) }}" target="_blank" class="text-blue-700 underline">View current</a></div>
                    @endif
                    <input type="file" name="medical_aid_card" accept="image/*,application/pdf" capture="environment" class="mt-1 w-full border rounded p-2" />
                    @error('medical_aid_card')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
                <a href="{{ route('guardian.children') }}" class="ml-3 text-blue-700 underline">Back</a>
            </div>
        </form>
    </div>
</x-app-layout>
