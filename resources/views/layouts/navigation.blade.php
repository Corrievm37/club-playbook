<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-[3.12rem] w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @guest
                    @php
                        $activeClubId = session('active_club_id');
                        $activeClub = $activeClubId ? \App\Models\Club::find($activeClubId) : null;
                        $routeClub = request()->route('club') ?? request()->route('clubSlug') ?? null;
                        $clubSlug = $activeClub?->slug ?? $routeClub;
                        $playerRegUrl = $clubSlug ? url('/register/'.$clubSlug.'/player') : route('register');
                    @endphp
                    <x-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')">
                        Login
                    </x-nav-link>
                    <x-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')">
                        Register
                    </x-nav-link>
                    @if($clubSlug)
                    <x-nav-link href="{{ $playerRegUrl }}">
                        Register Player
                    </x-nav-link>
                    @endif
                    @endguest
                    @auth
                    @role('org_admin')
                    <x-nav-link href="{{ route('superadmin.dashboard') }}" :active="request()->routeIs('superadmin.dashboard')">
                        Super Admin
                    </x-nav-link>
                    <x-nav-link href="{{ route('superadmin.users.index') }}" :active="request()->routeIs('superadmin.users.*')">
                        Users
                    </x-nav-link>
                    <x-nav-link href="{{ route('superadmin.memberships.index') }}" :active="request()->routeIs('superadmin.memberships.*')">
                        Memberships
                    </x-nav-link>
                    <x-nav-link href="{{ route('admin.clubs.index') }}" :active="request()->routeIs('admin.clubs.*')">
                        Clubs
                    </x-nav-link>
                    @endrole
                    {{-- Team dropdown replaces standalone Players/Teams links --}}
                    @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
                    <div class="relative flex items-center">
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 focus:outline-none focus:border-indigo-400 transition duration-150 ease-in-out sm:-my-px">
                                <span>Team</span>
                                <svg class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.25 8.29a.75.75 0 01-.02-1.08z" clip-rule="evenodd"/></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="py-1">
                                @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
                                    <x-dropdown-link :href="route('admin.players.index')" :active="request()->routeIs('admin.players.*')">
                                        Players
                                    </x-dropdown-link>
                                @endif
                                @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']))
                                    <x-dropdown-link :href="route('admin.coaches.index')" :active="request()->routeIs('admin.coaches.*')">
                                        Coaches
                                    </x-dropdown-link>
                                @endif
                                <x-dropdown-link :href="route('admin.teams.index')" :active="request()->routeIs('admin.teams.*')">
                                    Teams
                                </x-dropdown-link>
                                @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
                                    <x-dropdown-link :href="route('admin.invoices.index')" :active="request()->routeIs('admin.invoices.*')">
                                        Invoices
                                    </x-dropdown-link>
                                @endif
                            </div>
                        </x-slot>
                    </x-dropdown>
                    </div>
                    @endif
                    @if(auth()->user()->hasAnyRole(['club_admin','club_manager']))
                    <x-nav-link href="{{ route('admin.team_managers.index') }}" :active="request()->routeIs('admin.team_managers.*')">
                        Team Managers
                    </x-nav-link>
                    <x-nav-link href="{{ route('admin.coaches.invitations.create') }}" :active="request()->routeIs('admin.coaches.invitations.*')">
                        Invite Coach
                    </x-nav-link>
                    @endif
                    @if(auth()->user()->hasAnyRole(['club_admin','club_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
                    <x-nav-link href="{{ route('admin.clubs.index') }}" :active="request()->routeIs('admin.clubs.*')">
                        Club Settings
                    </x-nav-link>
                    @endif
                    @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
                    <x-nav-link href="{{ route('admin.notices.create') }}" :active="request()->routeIs('admin.notices.*')">
                        Post Notice
                    </x-nav-link>
                    @endif
                    @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager','coach']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
                    <x-nav-link href="{{ route('admin.attendance.index') }}" :active="request()->routeIs('admin.attendance.*')">
                        Attendance
                    </x-nav-link>
                    @endif
                    @if(auth()->user()->hasRole('team_manager'))
                        <x-nav-link href="{{ route('admin.coaches.assign') }}" :active="request()->routeIs('admin.coaches.assign*')">
                            Assign Coach
                        </x-nav-link>
                    @endif
                    @if(auth()->user()->hasAnyRole(['club_admin','club_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
                        <x-nav-link href="{{ route('admin.clubs.index') }}" :active="request()->routeIs('admin.clubs.*')">
                            Club Settings
                        </x-nav-link>
                    @php
                        $activeClubId = session('active_club_id');
                        $activeClub = $activeClubId ? \App\Models\Club::find($activeClubId) : null;
                        $regUrl = $activeClub ? url('/register/'.$activeClub->slug.'/player') : null;
                    @endphp
                    @if(auth()->user()->hasRole('team_manager') && $regUrl)
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $regUrl }}');" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 rounded-md text-gray-600 hover:text-gray-800">
                            Copy Registration Link
                        </button>
                    @endif
                    @endif
                    @if(auth()->user()->hasRole('guardian'))
                    <x-nav-link href="{{ route('guardian.children') }}" :active="request()->routeIs('guardian.children*')">
                        My Children
                    </x-nav-link>
                    <x-nav-link href="{{ route('guardian.sessions') }}" :active="request()->routeIs('guardian.sessions')">
                        Sessions
                    </x-nav-link>
                    <x-nav-link href="{{ route('guardian.invoices.index') }}" :active="request()->routeIs('guardian.invoices.*')">
                        My Invoices
                    </x-nav-link>
                    @endif
                    @endauth
                    @auth
                    <button id="enablePush" type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 rounded-md text-gray-600 hover:text-gray-800">
                        Enable Notifications
                    </button>
                    @endauth
                </div>
            </div>

            <!-- Active Club Selector moved to footer -->

            <!-- Settings Dropdown -->
            @auth
            <div class="hidden sm:flex sm:items-center sm:ms-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ auth()->user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        @if(auth()->user()->hasRole('coach'))
                        <x-dropdown-link :href="route('coach.profile.edit')">
                            Coach Profile
                        </x-dropdown-link>
                        @endif

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @if(session('impersonating_club_id'))
        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 text-sm flex items-center justify-between">
            <div>
                Impersonating club ID: {{ session('impersonating_club_id') }}
            </div>
            <form method="POST" action="{{ route('superadmin.impersonate.stop') }}">
                @csrf
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded">Stop</button>
            </form>
        </div>
    @endif

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @php
                    $activeClubId = session('active_club_id');
                    $memberships = \App\Models\Membership::with('club')->where('user_id', Auth::id())->get();
                @endphp
                @if($memberships->count() > 0)
                    <form method="POST" action="{{ route('admin.active-club.set') }}" class="px-4 mb-2">
                        @csrf
                        <label class="text-sm text-gray-600 mr-2">Active Club</label>
                        <select name="club_id" class="border rounded p-1 text-sm w-full" onchange="this.form.submit()">
                            @foreach($memberships as $m)
                                <option value="{{ $m->club->id }}" {{ (int)$activeClubId === (int)$m->club->id ? 'selected' : '' }}>{{ $m->club->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
            @endauth
            @guest
            @php
                $activeClubId = session('active_club_id');
                $activeClub = $activeClubId ? \App\Models\Club::find($activeClubId) : null;
                $routeClub = request()->route('club') ?? request()->route('clubSlug') ?? null;
                $clubSlug = $activeClub?->slug ?? $routeClub;
                $playerRegUrl = $clubSlug ? url('/register/'.$clubSlug.'/player') : route('register');
            @endphp
            <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                Login
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                Register
            </x-responsive-nav-link>
            @if($clubSlug)
            <x-responsive-nav-link href="{{ $playerRegUrl }}">
                Register Player
            </x-responsive-nav-link>
            @endif
            @endguest
            @auth
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @role('org_admin')
            <x-responsive-nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')">
                Super Admin
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('superadmin.users.index')" :active="request()->routeIs('superadmin.users.*')">
                Users
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('superadmin.memberships.index')" :active="request()->routeIs('superadmin.memberships.*')">
                Memberships
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.clubs.index')" :active="request()->routeIs('admin.clubs.*')">
                Clubs
            </x-responsive-nav-link>
            @endrole
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
            <x-responsive-nav-link :href="route('admin.fees.index')" :active="request()->routeIs('admin.fees.*')">
                Fees
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.team_managers.index')" :active="request()->routeIs('admin.team_managers.*')">
                Team Managers
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('admin.coaches.invitations.create')" :active="request()->routeIs('admin.coaches.invitations.*')">
                Invite Coach
            </x-responsive-nav-link>
            @endif
            <div class="px-4 pt-2 pb-1 text-xs uppercase text-gray-500">Team</div>
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
            <x-responsive-nav-link :href="route('admin.players.index')" :active="request()->routeIs('admin.players.*')">
                Players
            </x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']))
            <x-responsive-nav-link :href="route('admin.coaches.index')" :active="request()->routeIs('admin.coaches.*')">
                Coaches
            </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('admin.teams.index')" :active="request()->routeIs('admin.teams.*')">
                Teams
            </x-responsive-nav-link>
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
            <x-responsive-nav-link :href="route('admin.clubs.index')" :active="request()->routeIs('admin.clubs.*')">
                Club Settings
            </x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
            <x-responsive-nav-link :href="route('admin.notices.create')" :active="request()->routeIs('admin.notices.*')">
                Post Notice
            </x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
            <x-responsive-nav-link :href="route('admin.invoices.index')" :active="request()->routeIs('admin.invoices.*')">
                Invoices
            </x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager','coach']) || (auth()->user()->hasRole('org_admin') && session('active_club_id')))
            <x-responsive-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')">
                Attendance
            </x-responsive-nav-link>
            @if(auth()->user()->hasAnyRole(['club_admin','club_manager','team_manager']))
            <x-responsive-nav-link :href="route('admin.coaches.index')" :active="request()->routeIs('admin.coaches.*')">
                Coaches
            </x-responsive-nav-link>
            @endif
            @if(auth()->user()->hasRole('team_manager'))
            <x-responsive-nav-link :href="route('admin.coaches.assign')" :active="request()->routeIs('admin.coaches.assign*')">
                Assign Coach
            </x-responsive-nav-link>
            @endif
            @php
                $activeClubId = session('active_club_id');
                $activeClub = $activeClubId ? \App\Models\Club::find($activeClubId) : null;
                $regUrl = $activeClub ? url('/register/'.$activeClub->slug.'/player') : null;
            @endphp
            @if(auth()->user()->hasRole('team_manager') && $regUrl)
            <div class="px-4">
                <button type="button" onclick="navigator.clipboard.writeText('{{ $regUrl }}');" class="w-full text-left inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 rounded-md text-gray-600 hover:text-gray-800">
                    Copy Registration Link
                </button>
            </div>
            @endif
            @endif
            @if(auth()->user()->hasRole('guardian'))
            <x-responsive-nav-link :href="route('guardian.children')" :active="request()->routeIs('guardian.children*')">
                My Children
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('guardian.sessions')" :active="request()->routeIs('guardian.sessions')">
                Sessions
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('guardian.invoices.index')" :active="request()->routeIs('guardian.invoices.*')">
                My Invoices
            </x-responsive-nav-link>
            @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                @if(Auth::user()->hasRole('coach'))
                <x-responsive-nav-link :href="route('coach.profile.edit')">
                    Coach Profile
                </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const btn = document.getElementById('enablePush');
  if (!btn || !('serviceWorker' in navigator) || !('PushManager' in window)) return;
  const vapid = @json(config('webpush.vapid.public_key'));
  const csrf = @json(csrf_token());
  function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) outputArray[i] = rawData.charCodeAt(i);
    return outputArray;
  }
  async function subscribe() {
    try {
      const perm = await Notification.requestPermission();
      if (perm !== 'granted') return alert('Notifications permission denied');
      const reg = await navigator.serviceWorker.register('/sw.js');
      const sub = await reg.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapid)
      });
      const body = JSON.stringify(sub);
      await fetch(@json(route('push.subscribe')), {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf},
        body
      });
      btn.textContent = 'Notifications Enabled';
      btn.disabled = true;
    } catch (e) {
      console.error('Push subscribe failed', e);
      alert('Failed to enable notifications');
    }
  }
  btn.addEventListener('click', subscribe);
});
</script>
