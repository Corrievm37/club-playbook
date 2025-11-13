<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fee extends Model
{
    protected $fillable = [
        'club_id','season_year','name','amount_cents','due_date','installment_plan','active'
    ];

    protected $casts = [
        'installment_plan' => 'array',
        'active' => 'boolean',
        'due_date' => 'date',
    ];

    public function club(): BelongsTo { return $this->belongsTo(Club::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
}
