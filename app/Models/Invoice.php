<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'club_id','player_id','fee_id','number','status','issue_date','due_date',
        'subtotal_cents','tax_cents','total_cents','balance_cents','pdf_path','sent_at',
        'proof_path','proof_uploaded_at','proof_uploaded_by'
    ];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function player(): BelongsTo { return $this->belongsTo(Player::class); }
    public function fee(): BelongsTo { return $this->belongsTo(Fee::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (!$invoice->issue_date) { $invoice->issue_date = now()->toDateString(); }
            if (!$invoice->number) {
                $year = now()->year;
                $prefix = 'INV-' . $year . '-';
                $last = static::where('number', 'like', $prefix.'%')
                    ->orderBy('id','desc')->value('number');
                $seq = 1;
                if ($last && preg_match('/(\d{4})$/', $last, $m)) { $seq = intval($m[1]) + 1; }
                $invoice->number = $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
            }
            $invoice->subtotal_cents = $invoice->subtotal_cents ?? 0;
            $invoice->tax_cents = $invoice->tax_cents ?? 0;
            $invoice->total_cents = $invoice->total_cents ?: ($invoice->subtotal_cents + $invoice->tax_cents);
            $invoice->balance_cents = $invoice->balance_cents ?: $invoice->total_cents;
        });

        static::saved(function (Invoice $invoice) {
            // Recompute balance from payments
            $paid = (int) $invoice->payments()->sum('amount_cents');
            $newBalance = max(0, (int)$invoice->total_cents - $paid);
            if ($newBalance !== (int)$invoice->balance_cents) {
                $invoice->balance_cents = $newBalance;
                if ($newBalance === 0 && $invoice->status !== 'paid') {
                    $invoice->status = 'paid';
                }
                $invoice->saveQuietly();
            }
        });
    }
}
