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
        // Mengekstrak ekstensi email (bagian setelah titik terakhir)
        $emailParts = explode('@', $value);
        $domainParts = explode('.', end($emailParts));

        // Ekstensi yang diizinkan
        $allowedExtensions = ['id', 'net', 'com'];

        // Periksa apakah ekstensi email termasuk dalam yang diizinkan
        return in_array(end($domainParts), $allowedExtensions);
    }

    public function message()
    {
        return 'The email address must have a valid extension (id, net, com).';
    }

}
