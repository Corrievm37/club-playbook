<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Support\AgeGroups;

class Player extends Model
{
    protected $fillable = [
        'club_id','first_name','last_name','dob','sa_id_number','gender',
        'shirt_size','shirt_handed_out','age_group','season_year','position_primary','position_secondary',
        'medical_conditions','allergies','consent_guardian',
        'status','registered_by_user_id','approved_by_user_id','approved_at','rejection_reason'
        ,'school_name','medical_aid_name','medical_aid_number','id_document_path','medical_aid_card_path'
    ];

    protected static function booted(): void
    {
        static::saving(function (Player $player) {
            if (!$player->season_year) {
                $player->season_year = (int) now()->year;
            }
            if ($player->dob) {
                $dob = Carbon::parse($player->dob);
                $player->age_group = AgeGroups::forDob($dob, (int) $player->season_year);
            }
        });
    }
}
