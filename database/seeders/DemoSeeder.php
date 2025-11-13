<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\Player;
use App\Models\Fee;
use App\Models\Invoice;
use App\Support\AgeGroups;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $club = Club::firstOrCreate([
            'slug' => 'demo-club'
        ], [
            'name' => 'Demo Rugby Club',
            'email' => 'info@democlub.co.za',
            'phone' => '+27 21 555 1234',
            'address_line1' => '1 Main Road',
            'city' => 'Cape Town',
            'province' => 'Western Cape',
            'postal_code' => '8001',
            'bank_account_name' => 'Demo Rugby Club',
            'bank_name' => 'FNB',
            'bank_account_number' => '123456789',
            'bank_branch_code' => '250655',
            'vat_number' => null,
        ]);

        $season = (int) now()->year;

        $dob = now()->subYears(13)->subMonths(2);
        $ageGroup = AgeGroups::forDob(Carbon::parse($dob), $season);

        $player = Player::create([
            'club_id' => $club->id,
            'first_name' => 'John',
            'last_name' => 'Nkosi',
            'dob' => $dob->toDateString(),
            'gender' => 'male',
            'season_year' => $season,
            'age_group' => $ageGroup,
            'consent_guardian' => true,
        ]);

        $fee = Fee::firstOrCreate([
            'club_id' => $club->id,
            'season_year' => $season,
            'name' => 'Registration ' . $season . ' U13',
        ], [
            'amount_cents' => 150000,
            'due_date' => now()->copy()->addDays(10)->toDateString(),
            'installment_plan' => null,
            'active' => true,
        ]);

        $prefix = 'INV-' . $season . '-';
        $last = \App\Models\Invoice::where('number','like',$prefix.'%')->orderBy('id','desc')->value('number');
        $seq = 1;
        if ($last && preg_match('/(\d{4})$/', $last, $m)) { $seq = intval($m[1]) + 1; }
        $number = $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);

        Invoice::create([
            'club_id' => $club->id,
            'player_id' => $player->id,
            'fee_id' => $fee->id,
            'number' => $number,
            'status' => 'sent',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->copy()->addDays(10)->toDateString(),
            'subtotal_cents' => $fee->amount_cents,
            'tax_cents' => 0,
            'total_cents' => $fee->amount_cents,
            'balance_cents' => $fee->amount_cents,
        ]);
    }
}
