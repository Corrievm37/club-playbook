<x-app-layout>
    <div class="container mx-auto p-6 max-w-3xl">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
        @endif

        <h1 class="text-2xl font-semibold mb-4">Create Attendance Session</h1>

        <form method="POST" action="{{ route('admin.attendance.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Type</label>
                    <select name="type" class="mt-1 w-full border rounded p-2" required>
                        <option value="practice">Practice</option>
                        <option value="game">Game</option>
                    </select>
                    @error('type')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Scheduled At</label>
                    <input type="datetime-local" name="scheduled_at" class="mt-1 w-full border rounded p-2" required />
                    @error('scheduled_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Age Group</label>
                    @if(!empty($managedAge))
                        <input type="text" value="{{ $managedAge }}" class="mt-1 w-full border rounded p-2 bg-gray-100" disabled />
                        <input type="hidden" name="age_group" value="{{ $managedAge }}" />
                    @else
                        <select name="age_group" class="mt-1 w-full border rounded p-2" required>
                            @foreach(["U6","U7","U8","U9","U10","U11","U12","U13","U14","U15","U16","U17","U18"] as $ag)
                                <option value="{{ $ag }}">{{ $ag }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('age_group')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Location</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="mt-1 w-full border rounded p-2" />
                    @error('location')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" class="mt-1 w-full border rounded p-2" />
                @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Notes</label>
                <textarea name="notes" class="mt-1 w-full border rounded p-2" rows="4">{{ old('notes') }}</textarea>
                @error('notes')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create</button>
                <a href="{{ route('admin.attendance.index') }}" class="ml-2">Back</a>
            </div>
        </form>
    </div>
</x-app-layout>
