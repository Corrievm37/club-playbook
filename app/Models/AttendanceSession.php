<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'club_id', 'age_group', 'type', 'title', 'scheduled_at', 'location', 'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
