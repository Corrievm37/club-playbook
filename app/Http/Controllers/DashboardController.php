<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Support\ActiveClub;
use App\Models\Notice;
use App\Models\Guardian;
use App\Models\Membership;

class DashboardController extends Controller
{
    public function index()
    {
        $clubId = ActiveClub::id();
        $user = Auth::user();
        if (!$clubId && $user) {
            $clubId = \App\Models\Membership::where('user_id', $user->id)->value('club_id');
            if (!$clubId) {
                $clubId = Guardian::where(function($q) use ($user){
                        $q->where('email', $user->email);
                        if ($user->id) $q->orWhere('user_id', $user->id);
                    })
                    ->whereHas('player')
                    ->with('player:id,club_id')
                    ->get()
                    ->pluck('player.club_id')
                    ->filter()
                    ->unique()
                    ->first();
            }
        }
        $groups = [];

        if ($user) {
            if ($user->hasRole('team_manager')) {
                $mem = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'team_manager')->first();
                if ($mem && $mem->managed_age_group) $groups[] = $mem->managed_age_group;
            } elseif ($user->hasRole('coach')) {
                $mem = Membership::where('user_id', $user->id)->where('club_id', $clubId)->where('role', 'coach')->first();
                if ($mem && $mem->coach_category) $groups[] = $mem->coach_category;
            } elseif ($user->hasRole('guardian')) {
                $playerGroups = Guardian::with('player')
                    ->where(function($q) use ($user){
                        $q->where('email', $user->email);
                        if ($user->id) $q->orWhere('user_id', $user->id);
                    })
                    ->get()
                    ->pluck('player')
                    ->filter(fn($p)=>$p && (int)$p->club_id === (int)$clubId)
                    ->pluck('age_group')
                    ->unique()
                    ->values()
                    ->all();
                $groups = array_values(array_filter($playerGroups));
            } else {
                // admins/managers: show all groups in club by default (handled by null age_group anyway)
            }
        }

        $now = now();
        $notices = Notice::where('club_id', $clubId)
            ->where(function($q) use ($groups){
                $q->whereNull('age_group');
                if (!empty($groups)) {
                    $q->orWhereIn('age_group', $groups);
                }
            })
            ->where(function($q) use ($now){
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function($q) use ($now){
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->filter(function($n) use ($user, $clubId) {
                if (empty($n->audience_roles)) return true; // show to all roles
                if (!$user) return false;
                $userRoles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
                // Include membership role (if any) for this club
                $memRoles = \App\Models\Membership::where('user_id', $user->id)
                    ->where('club_id', $clubId)
                    ->pluck('role')
                    ->toArray();
                $userRoles = array_unique(array_merge($userRoles, $memRoles));
                // Implicit guardian if linked via guardians table in this club
                $isGuardianLinked = \App\Models\Guardian::where(function($q) use ($user){
                        $q->where('email', $user->email);
                        if ($user->id) $q->orWhere('user_id', $user->id);
                    })
                    ->whereHas('player', function($q) use ($clubId){ $q->where('club_id', $clubId); })
                    ->exists();
                if ($isGuardianLinked && !in_array('guardian', $userRoles, true)) {
                    $userRoles[] = 'guardian';
                }
                foreach ($n->audience_roles as $r) {
                    if (in_array($r, $userRoles, true)) return true;
                }
                return false;
            })
            ->values();

        return view('dashboard', compact('notices'));
    }
}
