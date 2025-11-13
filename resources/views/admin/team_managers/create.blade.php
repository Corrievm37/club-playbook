<x-app-layout>
    <div class="container mx-auto p-6 max-w-2xl">
        <h1 class="text-2xl font-semibold mb-4">Add Team Manager — {{ $club->name }}</h1>
        <form method="POST" action="{{ route('admin.team_managers.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2" required />
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full border rounded p-2" required />
                @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Managed Age Group</label>
                <select name="managed_age_group" class="mt-1 w-full border rounded p-2" required>
                    @foreach(['U6','U7','U8','U9','U10','U11','U12','U13','U14','U15','U16','U17','U18','U19'] as $ag)
                        <option value="{{ $ag }}" {{ old('managed_age_group')===$ag ? 'selected' : '' }}>{{ $ag }}</option>
                    @endforeach
                </select>
                @error('managed_age_group')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Password</label>
                <input type="password" name="password" class="mt-1 w-full border rounded p-2" required />
                @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Create</button>
                <a href="{{ route('admin.team_managers.index') }}" class="ml-2">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
