<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'name','slug','email','phone','address_line1','address_line2','city','province','postal_code','country','logo_url','branding',
        'bank_account_name','bank_name','bank_account_number','bank_branch_code','vat_number'
    ];

    protected $casts = [
        'branding' => 'array',
    ];

    public function fees(): HasMany { return $this->hasMany(Fee::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
}
