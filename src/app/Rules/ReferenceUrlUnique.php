<?php

namespace App\Rules;

use App\Models\NeoExpert;
use App\Models\RioExpert;
use Illuminate\Contracts\Validation\Rule;

class ReferenceUrlUnique implements Rule
{
    /**
     * Rio/Neo experts id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Account Type: neo or rio.
     *
     * @var string
     */
    protected $account;

    /**
     * Create a new rule instance.
     *
     * @param  mixed  $productId
     * @param  string  $accountType
     * @return void
     */
    public function __construct($productId, $accountType)
    {
        $this->id = $productId;
        $this->account = $accountType;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $URL
     * @return bool
     */
    public function passes($attribute, $URL)
    {
        $commodityId = (int) $this->id;

        // Check if URL already exists in RIO
        $hasDuplicateRioExpert = RioExpert::where('id', '<>', $commodityId)
            ->where('information', 'LIKE', "%{$URL}%")
            ->exists();

        // Return false on duplicate expert under rio
        if ($hasDuplicateRioExpert) {
            return false;
        }

        // Check if URL already exists in NEO
        $hasDuplicateNeoExpert = NeoExpert::where('id', '<>', $commodityId)
            ->where('information', 'LIKE', "%{$URL}%")
            ->exists();

        // Return false on duplicate expert under neo
        if ($hasDuplicateNeoExpert) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return array|string|null
     */
    public function message()
    {
        return __('The reference URL string has already been used.');
    }
}
