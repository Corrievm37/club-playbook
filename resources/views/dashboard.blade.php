<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Notice Board</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(isset($notices) && $notices->count() > 0)
                <div class="space-y-4">
                    @foreach($notices as $n)
                        <div class="bg-white shadow-sm rounded p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold">{{ $n->title }}</h3>
                                <div class="text-xs text-gray-500">{{ $n->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                            @if($n->age_group)
                                <div class="text-xs text-gray-600 mt-1">Age Group: {{ $n->age_group }}</div>
                            @else
                                <div class="text-xs text-gray-600 mt-1">All Age Categories</div>
                            @endif
                            <div class="mt-2 whitespace-pre-wrap">{{ $n->body }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white shadow-sm rounded p-6 text-gray-600">No notices yet.</div>
            @endif
        </div>
    </div>
</x-app-layout>
