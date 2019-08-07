<?php

namespace App\Http\Requests;

/**
 * Class StoreCompanyData
 * @package App\Http\Requests
 */
class StoreCompanyData extends DynamicMetaFormRequest
{
    /**
     * Loads validation rules from object meta scheme
     * @param int $clientId
     */
    public function initializeRules(int $clientId): void
    {
        return;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}