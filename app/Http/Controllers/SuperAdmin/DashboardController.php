<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use App\Models\Player;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'clubs' => Club::count(),
            'users' => User::count(),
            'registrations_pending' => Player::where('status','pending')->count(),
            'invoices_overdue' => Invoice::where('status','overdue')->count(),
        ];
        $recentClubs = Club::orderByDesc('id')->limit(5)->get();
        $recentPlayers = Player::orderByDesc('id')->limit(5)->get();
        return view('superadmin.dashboard', compact('stats','recentClubs','recentPlayers'));
    }
}
