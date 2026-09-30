<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAmount implements ValidationRule
{

     public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        if (!is_numeric($value)) {
            $fail('Nieprawidłowy format kwoty.');
            return;
        }

        $value = (string) $value;

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $value)) {
            $fail('Nieprawidłowy format kwoty.');
            return;
        }

        if ((float) $value <= 0) {
            $fail('Kwota musi być większa od 0.');
        }
    }

}
