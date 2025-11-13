<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Coach Profile</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-green-700">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('coach.profile.update') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First name</label>
                            @php($nameParts = explode(' ', $user->name ?? '', 2))
                            <input type="text" name="first_name" value="{{ old('first_name', $nameParts[0] ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
                            @error('first_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last name</label>
                            <input type="text" name="last_name" value="{{ old('last_name', $nameParts[1] ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
                            @error('last_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">South African ID Number</label>
                            <input type="text" name="id_number" value="{{ old('id_number', $user->id_number) }}" class="mt-1 block w-full border rounded p-2" required>
                            @error('id_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">BokSmart Number</label>
                            <input type="text" name="boksmart_number" value="{{ old('boksmart_number', $user->boksmart_number) }}" class="mt-1 block w-full border rounded p-2">
                            @error('boksmart_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">New Password (optional)</label>
                            <input type="password" name="password" class="mt-1 block w-full border rounded p-2">
                            @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <x-primary-button>Save Profile</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">My Qualifications</h3>
                <form method="POST" action="{{ route('coach.qualifications.store') }}" enctype="multipart/form-data" class="mb-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g. World Rugby Level 1" required>
                            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File</label>
                            <input type="file" name="file" accept="application/pdf,image/*" class="mt-1 block w-full" required>
                            @error('file')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-primary-button>Add Qualification</x-primary-button>
                    </div>
                </form>

                @if($quals->count() === 0)
                    <div class="text-gray-600">No qualifications uploaded yet.</div>
                @else
                    <ul class="divide-y">
                        @foreach($quals as $q)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $q->title ?? $q->original_name }}</div>
                                    <a href="{{ asset('storage/'.$q->file_path) }}" target="_blank" class="text-sm text-blue-700">View</a>
                                </div>
                                <form method="POST" action="{{ route('coach.qualifications.destroy', $q->id) }}" onsubmit="return confirm('Remove this qualification?');">
                                    @csrf
                                    <x-danger-button>Delete</x-danger-button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
