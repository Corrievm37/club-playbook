<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SouthAfricanId implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $id = preg_replace('/\D+/', '', (string)$value);
        if (strlen($id) !== 13) {
            $fail('The :attribute must be 13 digits.');
            return;
        }
        if (!$this->luhnCheck($id)) {
            $fail('The :attribute is not a valid South African ID number.');
            return;
        }
    }

    /**
     * Luhn checksum for SA ID numbers (13 digits).
     */
    private function luhnCheck(string $digits): bool
    {
        $sum = 0;
        $alt = false;
        // Process right-to-left
        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $n = intval($digits[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) $n -= 9;
            }
            $sum += $n;
            $alt = !$alt;
        }
        return $sum % 10 === 0;
    }
}
