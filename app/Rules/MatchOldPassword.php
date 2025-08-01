<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MatchOldPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!Hash::check($value, Auth::user()->password)) {
            $fail(__('validation.old_password_incorrect'));
        }
    }
}
