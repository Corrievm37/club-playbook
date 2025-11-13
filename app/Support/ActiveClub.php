<?php

namespace App\Support;

use App\Models\Membership;
use Illuminate\Support\Facades\Auth;

class ActiveClub
{
    public static function id(): ?int
    {
        return session('active_club_id');
    }

    public static function set(int $clubId): void
    {
        // Ensure user is a member of this club or is org_admin
        $user = Auth::user();
        if (!$user) return;
        if ($user->hasRole('org_admin')) {
            session(['active_club_id' => $clubId]);
            return;
        }
        $has = Membership::where('user_id', $user->id)->where('club_id', $clubId)->exists();
        if ($has) {
            session(['active_club_id' => $clubId]);
        }
    }
}
