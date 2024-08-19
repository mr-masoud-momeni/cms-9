<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class WhiteList implements Rule
{
    protected $params;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
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
        foreach ($this->params as $param){
            if ($value == $param) {
                $result = true;
                break;
            }
            else{
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('message.WhiteList');
    }
}
