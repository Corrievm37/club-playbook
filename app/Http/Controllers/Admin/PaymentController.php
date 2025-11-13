<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Payment;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        abort_unless(Auth::user()->hasAnyRole(['club_admin','club_manager','team_manager','org_admin']), 403);
        $invoiceId = $request->route('invoice');
        $invoice = Invoice::where('club_id', $clubId)->findOrFail($invoiceId);
        $data = $request->validate([
            'method' => 'required|in:cash,eft,other',
            'amount' => 'required|numeric|min:0.01',
            'paid_at' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);
        $amountCents = (int) round($data['amount'] * 100);
        $data['invoice_id'] = $invoice->id;
        $data['received_by'] = auth()->id();
        Payment::create([
            'invoice_id' => $data['invoice_id'],
            'method' => $data['method'],
            'amount_cents' => $amountCents,
            'paid_at' => $data['paid_at'],
            'reference' => $data['reference'] ?? null,
            'note' => $data['note'] ?? null,
            'received_by' => $data['received_by'],
        ]);
        return redirect()->route('admin.invoices.show', $invoice->id)->with('status', 'Payment captured.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
