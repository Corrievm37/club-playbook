@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Add Child</h1>

    @if (session('status'))
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('guardian.children.store') }}" enctype="multipart/form-data" class="space-y-4 bg-white shadow rounded p-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" class="mt-1 w-full border rounded p-2" required />
            @error('first_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" class="mt-1 w-full border rounded p-2" required />
            @error('last_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Date of Birth</label>
            <input type="date" name="dob" value="{{ old('dob') }}" class="mt-1 w-full border rounded p-2" required />
            @error('dob')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">Gender</label>
            <select name="gender" class="mt-1 w-full border rounded p-2">
                <option value="">Select</option>
                <option value="male" @selected(old('gender')==='male')>Male</option>
                <option value="female" @selected(old('gender')==='female')>Female</option>
                <option value="other" @selected(old('gender')==='other')>Other</option>
            </select>
            @error('gender')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">South African ID Number</label>
            <input type="text" name="sa_id_number" value="{{ old('sa_id_number') }}" class="mt-1 w-full border rounded p-2" />
            @error('sa_id_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium">School Name</label>
            <input type="text" name="school_name" value="{{ old('school_name') }}" class="mt-1 w-full border rounded p-2" />
            @error('school_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Medical Aid Name</label>
                <input type="text" name="medical_aid_name" value="{{ old('medical_aid_name') }}" class="mt-1 w-full border rounded p-2" />
                @error('medical_aid_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Medical Aid Number</label>
                <input type="text" name="medical_aid_number" value="{{ old('medical_aid_number') }}" class="mt-1 w-full border rounded p-2" />
                @error('medical_aid_number')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">ID Document (PDF/JPG/PNG, max 5MB)</label>
                <input type="file" name="id_document" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 w-full border rounded p-2" />
                @error('id_document')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium">Medical Aid Card (PDF/JPG/PNG, max 5MB)</label>
                <input type="file" name="medical_aid_card" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 w-full border rounded p-2" />
                @error('medical_aid_card')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Child</button>
            <a href="{{ route('guardian.children') }}" class="text-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
