<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clubs = Club::orderBy('name')->paginate(20);
        return view('admin.clubs.index', compact('clubs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.clubs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:clubs,slug',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:5120',
        ]);
        // Create first to get an ID for deterministic filename
        $club = Club::create(collect($data)->except('logo')->toArray());
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $ext = strtolower($file->getClientOriginalExtension());
            $base = Str::slug($data['slug'] ?? $data['name'] ?? 'club');
            $stableBase = $base.'-'.$club->id; // stable base without extension
            // Ensure directory exists
            Storage::disk('public')->makeDirectory('uploads/club_logos');
            // Remove any previous files with same stable base but different ext
            foreach (Storage::disk('public')->files('uploads/club_logos') as $existing) {
                if (str_starts_with(basename($existing), $stableBase.'.')) {
                    Storage::disk('public')->delete($existing);
                }
            }
            $filename = $stableBase.'.'.$ext; // stable name
            $relative = 'uploads/club_logos/'.$filename;
            Storage::disk('public')->putFileAs('uploads/club_logos', $file, $filename);
            $club->logo_url = $relative; // relative path on public disk
            $club->save();
        }
        return redirect()->route('admin.clubs.edit', $club->id)->with('status', 'Club created. You can now complete settings.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $club = Club::findOrFail($id);
        return view('admin.club.edit', compact('club'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $club = Club::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:clubs,slug,' . $club->id,
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_branch_code' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:5120',
        ]);
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $ext = strtolower($file->getClientOriginalExtension());
            $base = Str::slug(($data['slug'] ?? $club->slug) ?: ($data['name'] ?? $club->name) ?: 'club');
            $stableBase = $base.'-'.$club->id; // stable base without extension
            // Ensure directory exists
            Storage::disk('public')->makeDirectory('uploads/club_logos');
            // Remove any previous files with same stable base but different ext
            foreach (Storage::disk('public')->files('uploads/club_logos') as $existing) {
                if (str_starts_with(basename($existing), $stableBase.'.')) {
                    Storage::disk('public')->delete($existing);
                }
            }
            $filename = $stableBase.'.'.$ext; // stable name
            $relative = 'uploads/club_logos/'.$filename;
            Storage::disk('public')->putFileAs('uploads/club_logos', $file, $filename);
            $data['logo_url'] = $relative;
        }
        $club->update(collect($data)->except('logo')->toArray());
        return redirect()->route('admin.clubs.edit', $club->id)->with('status', 'Club settings saved.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
