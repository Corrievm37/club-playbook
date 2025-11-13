<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none !important;}</style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="pb-16">
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>
        </div>

        @php
            $activeClubId = session('active_club_id');
            $footerClub = $activeClubId ? \App\Models\Club::find($activeClubId) : null;
        @endphp
        @if($footerClub && $footerClub->logo_url)
            <div class="fixed bottom-0 inset-x-0 bg-white/80 backdrop-blur border-t border-gray-200 py-2 z-40">
                <div class="max-w-7xl mx-auto px-4 grid grid-cols-3 items-center">
                    @auth
                        @php
                            $memberships = \App\Models\Membership::with('club')->where('user_id', Auth::id())->get();
                        @endphp
                        @if($memberships->count() > 0)
                            <form method="POST" action="{{ route('admin.active-club.set') }}" class="justify-self-start">
                                @csrf
                                <label class="text-xs text-gray-600 mr-2">Active Club</label>
                                <select name="club_id" class="border rounded p-1 text-xs" onchange="this.form.submit()">
                                    @foreach($memberships as $m)
                                        <option value="{{ $m->club->id }}" {{ (int)$activeClubId === (int)$m->club->id ? 'selected' : '' }}>{{ $m->club->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    @endauth
                    <div class="justify-self-center">
                        <div class="bg-white/90 rounded-full shadow ring-1 ring-gray-200 px-3 py-1">
                            <img src="{{ asset('storage/'.$footerClub->logo_url) }}" alt="{{ $footerClub->name }} logo" class="h-10 sm:h-12 w-auto" />
                        </div>
                    </div>
                    <div class="justify-self-end"></div>
                </div>
            </div>
        @endif
    </body>
</html>
