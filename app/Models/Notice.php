<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id','title','body','age_group','audience_roles','created_by','starts_at','ends_at'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'audience_roles' => 'array',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
