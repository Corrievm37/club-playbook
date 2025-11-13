<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Notice</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-green-700">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('admin.notices.update', $notice->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" value="{{ old('title', $notice->title) }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Body</label>
                        <textarea name="body" rows="6" class="mt-1 block w-full border rounded p-2" required>{{ old('body', $notice->body) }}</textarea>
                        @error('body')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Age Group</label>
                            @php $canAll = Auth::user()->hasAnyRole(['club_admin','club_manager']); @endphp
                            @php $selectedAge = old('age_group', $notice->age_group ?? '') @endphp
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
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Audience Roles (who should see)</label>
                            @php $selectedRoles = collect(old('audience_roles', $notice->audience_roles ?? [])); @endphp
                            <select name="audience_roles[]" multiple class="mt-1 block w-full border rounded p-2">
                                @foreach($roleOptions as $r)
                                    <option value="{{ $r }}" {{ $selectedRoles->contains($r) ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $r)) }}</option>
                                @endforeach
                            </select>
                            <div class="text-xs text-gray-600 mt-1">Leave empty to show to all roles in the selected age group.</div>
                            @error('audience_roles')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Starts At (optional)</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($notice->starts_at)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border rounded p-2">
                            @error('starts_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ends At (optional)</label>
                            <input type="datetime-local" name="ends_at" value="{{ old('ends_at', optional($notice->ends_at)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full border rounded p-2">
                            @error('ends_at')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <x-primary-button>Update Notice</x-primary-button>
                        <a href="{{ route('admin.notices.create') }}" class="inline-flex items-center px-4 py-2 border rounded">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
