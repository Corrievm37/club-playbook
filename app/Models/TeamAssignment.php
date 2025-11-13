<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamAssignment extends Model
{
    protected $fillable = [
        'attendance_session_id', 'team_id', 'player_id', 'jersey_number'
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
