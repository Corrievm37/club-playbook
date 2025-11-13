<x-app-layout>
    <div class="container mx-auto p-6 max-w-2xl">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <h1 class="text-2xl font-semibold mb-4">Create Team</h1>

        <form method="POST" action="{{ route('admin.teams.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Team Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full border rounded p-2" required />
                    @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Age Group</label>
                    @if(!empty($managedAge))
                        <input type="text" value="{{ $managedAge }}" class="mt-1 w-full border rounded p-2 bg-gray-100" disabled />
                        <input type="hidden" name="age_group" value="{{ $managedAge }}" />
                    @else
                        <select name="age_group" class="mt-1 w-full border rounded p-2" required>
                            @foreach(["U6","U7","U8","U9","U10","U11","U12","U13","U14","U15","U16","U17","U18"] as $ag)
                                <option value="{{ $ag }}" {{ old('age_group')===$ag?'selected':'' }}>{{ $ag }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('age_group')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
                <a href="{{ route('admin.teams.index') }}" class="ml-2">Back</a>
            </div>
        </form>
    </div>
</x-app-layout>
