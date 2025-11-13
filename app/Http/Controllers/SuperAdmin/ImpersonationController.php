<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Support\ActiveClub;

class ImpersonationController extends Controller
{
    public function start(Request $request, Club $club)
    {
        // Set active club and mark impersonation
        ActiveClub::set($club->id);
        session(['impersonating_club_id' => $club->id]);
        return redirect()->route('admin.players.index')->with('status', 'Now impersonating '.$club->name);
    }

    public function stop(Request $request)
    {
        session()->forget('impersonating_club_id');
        session()->forget('active_club_id');
        return redirect()->route('superadmin.dashboard')->with('status', 'Stopped impersonation.');
    }
}
