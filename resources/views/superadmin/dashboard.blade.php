<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">Super Admin Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 shadow rounded">
                <div class="text-sm text-gray-600">Clubs</div>
                <div class="text-2xl font-bold">{{ $stats['clubs'] }}</div>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <div class="text-sm text-gray-600">Users</div>
                <div class="text-2xl font-bold">{{ $stats['users'] }}</div>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <div class="text-sm text-gray-600">Pending Registrations</div>
                <div class="text-2xl font-bold">{{ $stats['registrations_pending'] }}</div>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <div class="text-sm text-gray-600">Overdue Invoices</div>
                <div class="text-2xl font-bold">{{ $stats['invoices_overdue'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold mb-2">Recent Clubs</h2>
                <ul class="list-disc pl-5">
                    @foreach($recentClubs as $c)
                        <li>{{ $c->name }} ({{ $c->slug }})</li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold mb-2">Recent Registrations</h2>
                <ul class="list-disc pl-5">
                    @foreach($recentPlayers as $p)
                        <li>{{ $p->last_name }}, {{ $p->first_name }} - {{ $p->age_group }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
