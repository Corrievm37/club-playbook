<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\CoachInvitation;
use App\Mail\CoachInviteMail;
use App\Support\ActiveClub;

class CoachInvitationController extends Controller
{
    public function create()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        return view('admin.coaches.invitations.create');
    }

    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $data = $request->validate([
            'email' => 'required|email',
            'category' => 'nullable|string|max:100',
        ]);
        $token = Str::random(40);
        $invite = CoachInvitation::create([
            'club_id' => $clubId,
            'email' => $data['email'],
            'category' => $data['category'] ?? null,
            'token' => $token,
            'expires_at' => now()->addDays(14),
        ]);
        Mail::to($invite->email)->queue(new CoachInviteMail($invite));
        $inviteUrl = route('coach.invite.accept', $invite->token);
        return redirect()->back()->with([
            'status' => 'Invitation sent.',
            'invite_url' => $inviteUrl,
        ]);
    }
}
