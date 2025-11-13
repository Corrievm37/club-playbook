<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\Player;
use App\Models\Guardian;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Rules\SouthAfricanId;
use App\Models\Fee;
use App\Models\Invoice;

class RegistrationController extends Controller
{
    public function create(string $club)
    {
        $club = Club::where('slug', $club)->firstOrFail();
        return view('registration.player', compact('club'));
    }

    public function store(Request $request, string $club)
    {
        $club = Club::where('slug', $club)->firstOrFail();
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'dob' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'shirt_size' => 'nullable|string|in:XS,S,M,L,XL,XXL',
            'sa_id_number' => ['nullable', new SouthAfricanId],
            'school_name' => 'nullable|string|max:255',
            'medical_aid_name' => 'nullable|string|max:255',
            'medical_aid_number' => 'nullable|string|max:255',
            'id_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'medical_aid_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'guardian_first_name' => 'required|string|max:100',
            'guardian_last_name' => 'required|string|max:100',
            'guardian_email' => 'nullable|email',
            'guardian_phone' => 'nullable|string|max:50',
            'consent_guardian' => 'accepted',
        ]);

        $idPath = null;
        $medPath = null;
        if ($request->hasFile('id_document')) {
            $idPath = $request->file('id_document')->store('uploads/id_documents', 'public');
        }
        if ($request->hasFile('medical_aid_card')) {
            $medPath = $request->file('medical_aid_card')->store('uploads/medical_cards', 'public');
        }

        $player = Player::create([
            'club_id' => $club->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'dob' => $data['dob'],
            'gender' => $data['gender'] ?? null,
            'shirt_size' => $data['shirt_size'] ?? null,
            'shirt_handed_out' => false,
            'sa_id_number' => $data['sa_id_number'] ?? null,
            'school_name' => $data['school_name'] ?? null,
            'medical_aid_name' => $data['medical_aid_name'] ?? null,
            'medical_aid_number' => $data['medical_aid_number'] ?? null,
            'id_document_path' => $idPath,
            'medical_aid_card_path' => $medPath,
            'season_year' => (int) now()->year,
            'consent_guardian' => true,
            'status' => 'pending',
            'registered_by_user_id' => auth()->id(),
        ]);

        Guardian::updateOrCreate(
            [
                'player_id' => $player->id,
                'user_id' => auth()->id(),
            ],
            [
                'first_name' => $data['guardian_first_name'],
                'last_name' => $data['guardian_last_name'],
                'relationship' => 'guardian',
                'email' => $data['guardian_email'] ?? (auth()->user()?->email ?? null),
                'phone' => $data['guardian_phone'] ?? null,
                'primary_contact' => true,
            ]
        );

        // Ensure the parent sees Guardian menu links
        if (auth()->check() && !auth()->user()->hasRole('guardian')) {
            auth()->user()->assignRole('guardian');
        }

        // Auto-generate an initial invoice for registration using latest fee for the club/season (only if none exists yet)
        $fee = Fee::where('club_id', $club->id)
            ->where('season_year', (int) now()->year)
            ->orderByDesc('id')
            ->first();
        $hasInvoice = Invoice::where('club_id', $club->id)
            ->where('player_id', $player->id)
            ->whereYear('issue_date', now()->year)
            ->exists();
        if ($fee && !$hasInvoice) {
            $invoice = Invoice::create([
                'club_id' => $club->id,
                'player_id' => $player->id,
                'fee_id' => $fee->id,
                'subtotal_cents' => $fee->amount_cents,
                'tax_cents' => 0,
                'status' => 'sent',
                'issue_date' => now()->toDateString(),
                'due_date' => now()->copy()->addDays(14)->toDateString(),
            ]);
            // Email the primary guardian a copy (CC all other guardians)
            try {
                $primary = \App\Models\Guardian::where('player_id', $player->id)
                    ->where('primary_contact', true)->first();
                $others = \App\Models\Guardian::where('player_id', $player->id)
                    ->where(function($q){ $q->whereNull('primary_contact')->orWhere('primary_contact', false); })
                    ->whereNotNull('email')
                    ->get();
                if ($primary && $primary->email) {
                    $ccEmails = $others->pluck('email')->filter()->values()->all();
                    $mailable = new \App\Mail\InvoiceIssuedMail($invoice);
                    $mailer = \Mail::to([$primary->email => trim(($primary->first_name.' '.$primary->last_name)) ?: $primary->email]);
                    if (count($ccEmails) > 0) { $mailer->cc($ccEmails); }
                    $mailer->send($mailable);
                }
            } catch (\Throwable $e) {}
        }

        return redirect()->route('registration.thankyou');
    }

    public function thankyou()
    {
        return view('registration.thankyou');
    }
}
