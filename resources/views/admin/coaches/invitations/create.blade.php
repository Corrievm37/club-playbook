<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Invite Coach</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="mb-4 text-green-700">{{ session('status') }}</div>
                @endif
                @if (session('invite_url'))
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invitation Link</label>
                        <div class="flex gap-2">
                            <input id="inviteLink" type="text" readonly class="flex-1 border rounded p-2 text-sm" value="{{ session('invite_url') }}">
                            <button type="button" id="copyInvite" class="inline-flex items-center px-3 py-2 border rounded bg-gray-100">Copy</button>
                            <a target="_blank" rel="noopener" href="https://wa.me/?text={{ urlencode('Please complete your coach profile: '.session('invite_url')) }}" class="inline-flex items-center px-3 py-2 border rounded bg-green-100">WhatsApp</a>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function(){
                            const btn = document.getElementById('copyInvite');
                            if (btn) btn.addEventListener('click', function(){
                                const input = document.getElementById('inviteLink');
                                input.select(); input.setSelectionRange(0, 99999);
                                navigator.clipboard.writeText(input.value).then(()=>{
                                    btn.textContent = 'Copied';
                                    setTimeout(()=>btn.textContent='Copy', 1500);
                                });
                            });
                        });
                    </script>
                @endif
                <form method="POST" action="{{ route('admin.coaches.invitations.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Coach Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border rounded p-2" required>
                        @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Category (Age group or role)</label>
                        <input type="text" name="category" value="{{ old('category') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g. U10, U12, Strength & Conditioning">
                        @error('category')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <div class="flex gap-3">
                        <x-primary-button>Send Invitation</x-primary-button>
                        <a href="{{ route('admin.teams.index') }}" class="inline-flex items-center px-4 py-2 border rounded">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
