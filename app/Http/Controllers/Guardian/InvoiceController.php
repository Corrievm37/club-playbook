<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Player;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $players = Guardian::with('player')
            ->where('email', $user->email)
            ->orWhere('user_id', $user->id ?? null)
            ->get()
            ->pluck('player')
            ->filter()
            ->pluck('id')
            ->all();
        $invoices = Invoice::with(['player','fee','club'])
            ->whereIn('player_id', $players)
            ->orderByDesc('id')
            ->paginate(20);
        return view('guardian.invoices.index', compact('invoices'));
    }

    public function show(string $invoiceId)
    {
        $invoice = Invoice::with(['player','fee','club','payments'])->findOrFail($invoiceId);
        $this->authorizeGuardianFor($invoice->player);
        return view('guardian.invoices.show', compact('invoice'));
    }

    public function uploadProof(Request $request, string $invoiceId)
    {
        $invoice = Invoice::with(['player'])->findOrFail($invoiceId);
        $this->authorizeGuardianFor($invoice->player);
        $data = $request->validate([
            'proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:8192',
        ]);
        $path = $request->file('proof')->store('uploads/payment_proofs', 'public');
        $invoice->proof_path = $path;
        $invoice->proof_uploaded_at = now();
        $invoice->proof_uploaded_by = Auth::id();
        // Set status to pending for review if not already paid
        if ($invoice->status !== 'paid') {
            $invoice->status = 'pending';
        }
        $invoice->save();
        return redirect()->route('guardian.invoices.show', $invoice->id)->with('status', 'Proof of payment uploaded.');
    }

    protected function authorizeGuardianFor(?Player $player): void
    {
        abort_unless($player, 404);
        $user = Auth::user();
        $isGuardian = Guardian::where('player_id', $player->id)
            ->where(function($q) use ($user){
                $q->where('email', $user->email);
                if ($user->id) $q->orWhere('user_id', $user->id);
            })
            ->exists();
        abort_unless($isGuardian, 403);
    }
}
