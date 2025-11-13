<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guardian extends Model
{
    protected $fillable = [
        'player_id','user_id','first_name','last_name','relationship','email','phone','primary_contact'
    ];

    protected $casts = [
        'primary_contact' => 'boolean',
    ];

    public function player(): BelongsTo { return $this->belongsTo(Player::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
