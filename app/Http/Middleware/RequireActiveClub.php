<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\ActiveClub;
use App\Models\Membership;

class RequireActiveClub
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->hasRole('org_admin')) {
            // Super admins may not have an active club unless impersonating
            return $next($request);
        }
        if (!ActiveClub::id()) {
            $user = auth()->user();
            // Try privileged roles first
            $privileged = Membership::where('user_id', $user->id)
                ->whereIn('role', ['club_admin','club_manager','team_manager','coach'])
                ->pluck('club_id')
                ->unique();
            if ($privileged->count() === 1) {
                ActiveClub::set((int)$privileged->first());
                return $next($request);
            }
            // Fallback: any membership and only one club overall
            $clubs = Membership::where('user_id', $user->id)->pluck('club_id')->unique();
            if ($clubs->count() === 1) {
                ActiveClub::set((int)$clubs->first());
                return $next($request);
            }
            return redirect()->route('dashboard')->with('status', 'Please select a club to manage.');
        }
        return $next($request);
    }
}
