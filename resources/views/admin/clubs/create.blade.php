<x-app-layout>
    <div class="container mx-auto p-6 max-w-3xl">
        <h1 class="text-2xl font-semibold mb-4">Create Club</h1>
        <form method="POST" action="{{ route('admin.clubs.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2" required />
                    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="mt-1 w-full border rounded p-2" required />
                    <div class="text-xs text-gray-600">Used in registration URL, e.g. /register/<strong>your-slug</strong>/player</div>
                    @error('slug')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded p-2" />
                    @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full border rounded p-2" />
                    @error('phone')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
                <a href="{{ route('admin.clubs.index') }}" class="ml-2">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
