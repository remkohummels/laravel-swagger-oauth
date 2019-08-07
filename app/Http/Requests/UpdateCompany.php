<?php

namespace App\Http\Requests;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompany extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('company')->id;
        return [
            Company::BASIC_GROUP . '.' . Company::NAME => 'filled|unique:companies,name,' . $id . '|min:2|max:128',
            Company::BASIC_GROUP . '.' . Company::ADDRESS => 'filled|string|min:3|max:256',
            Company::BASIC_GROUP . '.' . Company::ZIP => 'nullable|string|min:3|max:8',
            Company::BASIC_GROUP . '.' . Company::PHONE => 'nullable|string|min:3|max:64',
            Company::BASIC_GROUP . '.' . Company::FAX => 'nullable|string|min:3|max:64',
            Company::BASIC_GROUP . '.' . Company::WEBSITE => 'nullable|url|min:3|max:128',
            Company::BASIC_GROUP . '.' . Company::LANGUAGE => 'nullable|string|min:2|max:64',
            Company::BASIC_GROUP . '.' . Company::LOCATION => 'nullable|string|min:3|max:64',
            Company::BASIC_GROUP . '.' . Company::DESCRIPTION => 'nullable|string|min:3|max:1024',
            Company::BASIC_GROUP . '.' . Company::SHORT_DESCRIPTION => 'nullable|string|min:3|max:512',
            Company::BASIC_GROUP . '.' . Company::OPENED_24_HOURS => 'nullable|boolean',
            Company::BASIC_GROUP . '.' . Company::PAYMENT_METHOD => 'nullable|string|min:3|max:128',
            Company::BASIC_GROUP . '.' . Company::FACEBOOK_URL => 'nullable|string|min:3|max:128',
            Company::BASIC_GROUP . '.' . Company::KEY_PERSON_NAME => 'nullable|string|min:3|max:128',
            Company::BASIC_GROUP . '.' . Company::KEY_PERSON_TITLE => 'nullable|string|min:3|max:128',
            Company::BASIC_GROUP . '.' . Company::KEY_PERSON_PHONE => 'nullable|string|min:3|max:128',
            Company::BASIC_GROUP . '.' . Company::KEY_PERSON_EMAIL => 'nullable|string|min:3|max:128',
            Company::BASIC_GROUP . '.' . Company::ELIGIBILITY_REQUIREMENT => 'nullable|string|min:3|max:512',
        ];
    }


}