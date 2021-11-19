<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MaxByte implements Rule
{
    private int $param;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $param)
    {
        $this->param = $param;
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
        return strlen($value) <= $this->param;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->param . 'byteを超えています';
    }
}
