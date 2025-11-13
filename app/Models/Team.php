<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'club_id','age_group','name','season_year'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function assignments()
    {
        return $this->hasMany(TeamAssignment::class);
    }

    public function coaches()
    {
        return $this->belongsToMany(User::class, 'team_coach')->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saving(function (Team $team) {
            if (!$team->season_year) {
                $team->season_year = (int) now()->year;
            }
        });
    }
}
