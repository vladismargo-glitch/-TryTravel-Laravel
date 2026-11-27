<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BcryptPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        if (strlen($value) > 72) {
            $fail('Пароль слишком длинный.');
        }

        if (str_contains($value, "\0")) {
            $fail('Пароль содержит недопустимый символ.');
        }

        if (! preg_match('/\p{L}/u', $value) || ! preg_match('/\p{Nd}/u', $value)) {
            $fail('Пароль должен содержать хотя бы одну букву и одну цифру.');
        }
    }
}
