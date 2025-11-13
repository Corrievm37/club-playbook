<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id','method','amount_cents','paid_at','reference','received_by','note'
    ];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }

    protected static function booted(): void
    {
        $recompute = function (Payment $payment) {
            if ($payment->invoice) {
                // Trigger invoice saved hook to recompute
                $payment->invoice->touch();
            }
        };
        static::created($recompute);
        static::updated($recompute);
        static::deleted($recompute);
    }
}
