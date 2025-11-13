<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Post Notice</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-green-700">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.notices.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Body</label>
                        <textarea name="body" rows="6" class="mt-1 block w-full border rounded p-2" required>{{ old('body') }}</textarea>
                        @error('body')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Age Group</label>
                            @php $canAll = Auth::user()->hasAnyRole(['club_admin','club_manager']); @endphp
                            @php $selectedAge = old('age_group', $managedAge ?? ''); @endphp
                            <select name="age_group" class="mt-1 block w-full border rounded p-2" {{ isset($managedAge) ? 'disabled' : '' }}>
                                @if($canAll)
                                    <option value="">All Age Categories</option>
                                @endif
                                @foreach($ageGroups as $ag)
                                    <option value="{{ $ag }}" {{ $selectedAge === $ag ? 'selected' : '' }}>{{ $ag }}</option>
                                @endforeach
                            </select>
                            @if(isset($managedAge))
                                <input type="hidden" name="age_group" value="{{ $managedAge }}">
                            @endif
                            @error('age_group')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                            @if(!$canAll)
                                <div class="text-xs text-gray-600 mt-1">You can only post to your managed age group.</div>
                            @endif
                        </div>
                        <div>
                            @php $isTeamManager = Auth::user()->hasRole('team_manager'); @endphp
                            <label class="block text-sm font-medium text-gray-700">Audience</label>
                            @if($isTeamManager)
                                <div class="mt-2">Guardians</div>
                                <input type="hidden" name="audience_roles[]" value="guardian">
                                <div class="text-xs text-gray-600 mt-1">Team managers can only post to Guardians of their managed age group.</div>
                            @else
                                @php $selectedRoles = collect(old('audience_roles', [])); @endphp
                                <select name="audience_roles[]" multiple class="mt-1 block w-full border rounded p-2">
                                    @foreach($roleOptions as $r)
                                        <option value="{{ $r }}" {{ $selectedRoles->contains($r) ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $r)) }}</option>
                                    @endforeach
                                </select>
                                <div class="text-xs text-gray-600 mt-1">Leave empty to show to all roles in the selected age group.</div>
                                @error('audience_roles')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Starts At (optional)</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="mt-1 block w-full border rounded p-2">
                            @error('starts_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ends At (optional)</label>
                            <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="mt-1 block w-full border rounded p-2">
                            @error('ends_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <x-primary-button>Post Notice</x-primary-button>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border rounded">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="py-2">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Recent Notices</h3>
                @if(isset($notices) && $notices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2 pr-4">Created</th>
                                    <th class="py-2 pr-4">Title</th>
                                    <th class="py-2 pr-4">Age Group</th>
                                    <th class="py-2 pr-4">Audience Roles</th>
                                    <th class="py-2 pr-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notices as $n)
                                <tr class="border-b">
                                    <td class="py-2 pr-4 text-gray-600">{{ $n->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="py-2 pr-4">{{ $n->title }}</td>
                                    <td class="py-2 pr-4">{{ $n->age_group ? $n->age_group : 'ALL' }}</td>
                                    <td class="py-2 pr-4">{{ empty($n->audience_roles) ? 'All roles' : implode(', ', array_map(fn($r)=>ucfirst(str_replace('_',' ',$r)), $n->audience_roles)) }}</td>
                                    <td class="py-2 pr-4 flex gap-2">
                                        <a href="{{ route('admin.notices.edit', $n->id) }}" class="text-blue-700">Edit</a>
                                        <form method="POST" action="{{ route('admin.notices.destroy', $n->id) }}" onsubmit="return confirm('Delete this notice?');">
                                            @csrf
                                            <button type="submit" class="text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-gray-600">No notices yet.</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
