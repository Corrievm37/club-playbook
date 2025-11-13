<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Club;
use App\Models\Player;
use App\Models\Fee;
use App\Support\ActiveClub;
use Illuminate\Support\Facades\Auth;
use App\Models\Membership;
use App\Models\Payment;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $club = Club::find($clubId);
        $user = Auth::user();
        $managedAge = null;
        if ($user && $user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                $managedAge = $membership->managed_age_group;
            }
        }
        $invoices = Invoice::with(['player','fee'])
            ->where('club_id', $clubId)
            ->when($managedAge, function ($q) use ($managedAge) {
                $q->whereHas('player', function ($qq) use ($managedAge) {
                    $qq->where('age_group', $managedAge);
                });
            })
            ->orderByDesc('id')
            ->paginate(20);
        return view('admin.invoices.index', compact('invoices','club'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $club = Club::findOrFail($clubId);
        $players = Player::where('club_id', $clubId)->orderBy('last_name')->get();
        $fees = Fee::where('club_id', $clubId)->orderByDesc('season_year')->get();
        return view('admin.invoices.create', compact('club','players','fees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $data = $request->validate([
            'player_id' => 'required|exists:players,id',
            'fee_id' => 'nullable|exists:fees,id',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'subtotal_cents' => 'nullable|integer|min:0',
            'tax_cents' => 'nullable|integer|min:0',
        ]);
        // Ensure selected player/fee belong to active club
        abort_unless(Player::where('club_id',$clubId)->where('id',$data['player_id'])->exists(), 422);
        if (!empty($data['fee_id'])) {
            abort_unless(Fee::where('club_id',$clubId)->where('id',$data['fee_id'])->exists(), 422);
        }
        $data['club_id'] = $clubId;
        if (empty($data['subtotal_cents']) && !empty($data['fee_id'])) {
            $fee = Fee::find($data['fee_id']);
            $data['subtotal_cents'] = $fee?->amount_cents ?? 0;
        }
        $invoice = Invoice::create($data + ['status' => 'sent']);
        return redirect()->route('admin.invoices.show', $invoice->id)->with('status', 'Invoice created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $invoice = Invoice::with(['player','fee','payments','club'])->where('club_id',$clubId)->findOrFail($id);

        // If team manager, enforce managed age group access
        $user = Auth::user();
        if ($user && $user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                abort_unless(optional($invoice->player)->age_group === $membership->managed_age_group, 403);
            }
        }
        return view('admin.invoices.show', compact('invoice'));
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
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $invoice = Invoice::where('club_id',$clubId)->findOrFail($id);
        $invoice->delete();
        return redirect()->route('admin.invoices.index')->with('status', 'Invoice deleted.');
    }

    public function confirmProof(Request $request, string $invoiceId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);
        $user = Auth::user();
        abort_unless($user && $user->hasAnyRole(['club_admin','club_manager','team_manager','org_admin']), 403);

        $invoice = Invoice::with(['player','payments'])->where('club_id', $clubId)->findOrFail($invoiceId);
        // Team manager scope: only confirm for their age group
        if ($user->hasRole('team_manager')) {
            $membership = Membership::where('user_id', $user->id)
                ->where('club_id', $clubId)
                ->where('role', 'team_manager')
                ->first();
            if ($membership && $membership->managed_age_group) {
                abort_unless(optional($invoice->player)->age_group === $membership->managed_age_group, 403);
            }
        }

        // Create a payment for the remaining balance to settle the invoice
        $balanceCents = (int) $invoice->balance_cents;
        if ($balanceCents > 0) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'method' => 'eft',
                'amount_cents' => $balanceCents,
                'paid_at' => now()->toDateString(),
                'reference' => 'Proof confirmed',
                'note' => 'Proof of payment reviewed and confirmed by manager',
                'received_by' => $user->id,
            ]);
        }
        $invoice->status = 'paid';
        $invoice->save();

        return redirect()->route('admin.invoices.show', $invoice->id)->with('status', 'Payment confirmed and invoice marked as paid.');
    }

    /**
     * Generate or refresh a registration invoice for the given player for the current season.
     */
    public function regenerateForPlayer(Request $request, string $playerId)
    {
        $clubId = ActiveClub::id();
        abort_unless($clubId, 403);

        $user = Auth::user();
        abort_unless($user && $user->hasAnyRole(['club_admin','club_manager','team_manager','org_admin']), 403);

        $player = Player::where('club_id', $clubId)->findOrFail($playerId);

        $season = (int) now()->year;
        $fee = Fee::where('club_id', $clubId)
            ->where('season_year', $season)
            ->orderByDesc('id')
            ->first();

        if (!$fee) {
            return back()->with('error', 'No registration fee configured for this club and season.');
        }

        $issueDate = now()->toDateString();
        $dueDate = now()->copy()->addDays(14)->toDateString();

        $invoice = Invoice::where('club_id', $clubId)
            ->where('player_id', $player->id)
            ->whereYear('issue_date', $season)
            ->orderByDesc('id')
            ->first();

        if ($invoice) {
            // Refresh existing invoice to match the current fee and dates
            $invoice->fee_id = $fee->id;
            $invoice->subtotal_cents = (int) $fee->amount_cents;
            $invoice->tax_cents = 0;
            $invoice->total_cents = (int) $fee->amount_cents;
            $invoice->balance_cents = max(0, (int) $invoice->total_cents - (int) $invoice->payments()->sum('amount_cents'));
            $invoice->status = $invoice->balance_cents > 0 ? 'sent' : 'paid';
            $invoice->issue_date = $issueDate;
            $invoice->due_date = $dueDate;
            $invoice->save();
        } else {
            $invoice = Invoice::create([
                'club_id' => $clubId,
                'player_id' => $player->id,
                'fee_id' => $fee->id,
                'subtotal_cents' => (int) $fee->amount_cents,
                'tax_cents' => 0,
                'status' => 'sent',
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('status', 'Registration invoice generated/refreshed.');
    }
}
