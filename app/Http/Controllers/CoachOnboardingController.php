<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\CoachInvitation;
use App\Models\CoachQualification;
use App\Models\User;
use App\Models\Membership;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Schema;

class CoachOnboardingController extends Controller
{
    public function show(string $token)
    {
        $invite = CoachInvitation::with('club')->where('token', $token)
            ->whereNull('redeemed_at')
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->firstOrFail();
        return view('coach.onboarding.form', ['invite' => $invite]);
    }

    public function submit(Request $request, string $token)
    {
        $invite = CoachInvitation::where('token', $token)
            ->whereNull('redeemed_at')
            ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->firstOrFail();

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'id_number' => 'required|string|max:30',
            'boksmart_number' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8',
            'qualifications.*' => 'file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Find or create user by email
        $user = User::where('email', $invite->email)->first();
        if (!$user) {
            $user = new User();
            $user->email = $invite->email;
            $user->password = Hash::make($data['password'] ?? Str::random(16));
        }
        $user->name = $data['first_name'].' '.$data['last_name'];
        $user->id_number = $data['id_number'];
        $user->boksmart_number = $data['boksmart_number'] ?? null;
        if (property_exists($user, 'coach_category')) {
            $user->coach_category = $invite->category;
        }
        $user->save();

        // Role and membership
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('coach');
        }
        // ensure membership with club
        if (class_exists(Membership::class)) {
            $membership = Membership::firstOrCreate([
                'club_id' => $invite->club_id,
                'user_id' => $user->id,
            ], [
                'role' => 'coach',
            ]);
            if (Schema::hasColumn($membership->getTable(), 'coach_category')) {
                $membership->coach_category = $invite->category;
                $membership->save();
            }
        }

        // Upload qualifications
        if ($request->hasFile('qualifications')) {
            foreach ($request->file('qualifications') as $file) {
                $path = $file->store('coach_qualifications', 'public');
                CoachQualification::create([
                    'user_id' => $user->id,
                    'club_id' => $invite->club_id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $invite->redeemed_at = now();
        $invite->user_id = $user->id;
        $invite->save();

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Coach profile completed.');
    }
}
