<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Sort implements Rule
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
        return -999 <= $value && $value <= 999;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '排序值必须大于等于-999且小于等于999';
    }
}
