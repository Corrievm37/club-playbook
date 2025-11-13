<?php

namespace App\Support;

use Carbon\Carbon;

class AgeGroups
{
    /**
     * Determine SA rugby age group (U6..U19) based on DOB and season (Jan 1 cutoff).
     */
    public static function forDob(Carbon $dob, int $seasonYear): string
    {
        // For Jan 1 cutoff, age is simply the season year minus birth year
        $age = $seasonYear - (int)$dob->year;
        if ($age < 6) { $age = 6; }
        if ($age > 19) { $age = 19; }
        return 'U' . $age;
    }
}
