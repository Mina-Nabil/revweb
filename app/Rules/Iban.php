<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Iban implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $first2Letters = substr($value, 0, 2);
        return !((is_numeric($first2Letters) || strtoupper($first2Letters) != $first2Letters || strlen($value) < 4 || strlen($value) > 35));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Iban Number supplied is invalid.';
    }
}
