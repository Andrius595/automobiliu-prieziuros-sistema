<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlateNumberRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(?=.*\d)(?!.*[Q|q])[a-zA-Z\d]{1,6}$/', $value)) {
            $fail("Valstybinių numerių formatas yra neteisingas.");
        }

    }
}
