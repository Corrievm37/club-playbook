<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\Club;
use App\Support\ActiveClub;

class FeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clubId = ActiveClub::id();
        $club = $clubId ? Club::find($clubId) : null;
        $fees = Fee::when($clubId, fn($q)=>$q->where('club_id', $clubId))->orderByDesc('season_year')->paginate(20);
        return view('admin.fees.index', compact('fees','club'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $club = ActiveClub::id() ? Club::find(ActiveClub::id()) : null;
        return view('admin.fees.create', compact('club'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $data = $request->validate([
            'season_year' => 'required|integer|min:2000|max:2100',
            'name' => 'required|string|max:255',
            'amount_cents' => 'required|integer|min:0',
            'due_date' => 'nullable|date',
            'active' => 'nullable|boolean',
        ]);
        $data['club_id'] = $clubId;
        $data['active'] = $request->boolean('active');
        Fee::create($data);
        return redirect()->route('admin.fees.index')->with('status', 'Fee created.');
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
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $club = Club::find($clubId);
        $fee = Fee::where('club_id',$clubId)->findOrFail($id);
        return view('admin.fees.edit', compact('fee','club'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $fee = Fee::where('club_id',$clubId)->findOrFail($id);
        $data = $request->validate([
            'season_year' => 'required|integer|min:2000|max:2100',
            'name' => 'required|string|max:255',
            'amount_cents' => 'required|integer|min:0',
            'due_date' => 'nullable|date',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $fee->update($data);
        return redirect()->route('admin.fees.index')->with('status', 'Fee updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $fee = Fee::where('club_id',$clubId)->findOrFail($id);
        $fee->delete();
        return redirect()->route('admin.fees.index')->with('status', 'Fee deleted.');
    }
}
