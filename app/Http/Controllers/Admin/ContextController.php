<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Support\ActiveClub;
use App\Models\Membership;

class ContextController extends Controller
{
    public function setActiveClub(Request $request)
    {
        $data = $request->validate([
            'club_id' => 'required|integer',
        ]);
        // Ensure membership or org_admin
        $user = $request->user();
        if ($user->hasRole('org_admin') || Membership::where('user_id', $user->id)->where('club_id', $data['club_id'])->exists()) {
            ActiveClub::set((int)$data['club_id']);
        }
        return back()->with('status', 'Active club updated.');
    }
}
