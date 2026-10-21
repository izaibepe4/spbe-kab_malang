<?php

namespace App\Http\Requests;

class UpdateLayananRequest extends StoreLayananRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Same rules as StoreLayananRequest
        return parent::rules();
    }
}
