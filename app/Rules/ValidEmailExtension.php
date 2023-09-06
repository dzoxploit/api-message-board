<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidEmailExtension implements Rule
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
        $allowedExtensions = ['.id', '.net', '.com'];
        $emailParts = explode('@', $value);

        if (count($emailParts) === 2) {
            $domain = $emailParts[1];

            foreach ($allowedExtensions as $extension) {
                if (ends_with($domain, $extension)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function message()
    {
        return 'Email harus berakhiran ".id", ".net", atau ".com".';
    }

}
