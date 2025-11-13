<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assign Coach to Team</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-green-700">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.coaches.assign.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Team</label>
                            <select name="team_id" class="mt-1 block w-full border rounded p-2" required>
                                <option value="">Select team...</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->age_group }} — {{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Coach</label>
                            <select name="user_id" class="mt-1 block w-full border rounded p-2" required>
                                <option value="">Select coach...</option>
                                @foreach($coaches as $coach)
                                    <option value="{{ $coach->id }}">{{ $coach->name }} ({{ $coach->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <x-primary-button>Assign</x-primary-button>
                        <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center px-4 py-2 border rounded">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
