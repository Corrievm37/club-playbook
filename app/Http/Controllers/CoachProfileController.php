<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\CoachQualification;
use App\Support\ActiveClub;

class CoachProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $clubId = ActiveClub::id();
        $quals = CoachQualification::where('user_id', $user->id)
            ->when($clubId, fn($q)=>$q->where('club_id', $clubId))
            ->orderByDesc('created_at')
            ->get();
        return view('coach.profile.edit', compact('user','quals','clubId'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'id_number' => 'required|string|max:30',
            'boksmart_number' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8',
        ]);
        $user->name = $data['first_name'].' '.$data['last_name'];
        $user->id_number = $data['id_number'];
        $user->boksmart_number = $data['boksmart_number'] ?? null;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();
        return back()->with('status','Profile updated');
    }

    public function storeQualification(Request $request)
    {
        $user = Auth::user();
        $clubId = ActiveClub::id();
        $data = $request->validate([
            'title' => 'required|string|max:120',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        $path = $request->file('file')->store('coach_qualifications', 'public');
        CoachQualification::create([
            'user_id' => $user->id,
            'club_id' => $clubId,
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'title' => $data['title'],
        ]);
        return back()->with('status','Qualification added');
    }

    public function destroyQualification(Request $request, string $id)
    {
        $user = Auth::user();
        $clubId = ActiveClub::id();
        $qual = CoachQualification::where('id', $id)
            ->where('user_id', $user->id)
            ->when($clubId, fn($q)=>$q->where('club_id', $clubId))
            ->firstOrFail();
        Storage::disk('public')->delete($qual->file_path);
        $qual->delete();
        return back()->with('status','Qualification removed');
    }
}
