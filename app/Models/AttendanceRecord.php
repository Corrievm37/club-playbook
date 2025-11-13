<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'attendance_session_id', 'player_id', 'rsvp_status', 'present', 'notes'
    ];

    protected $casts = [
        'present' => 'boolean',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
