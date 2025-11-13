<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'club_id', 'file_path', 'original_name', 'title'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
