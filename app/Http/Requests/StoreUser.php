<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreUser
 * @package App\Http\Requests
 */
class StoreUser extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            User::BASIC_GROUP . '.' . User::NAME => 'required|min:2|max:64',
            User::BASIC_GROUP . '.' . User::FIRST_NAME => 'required|min:3|max:128',
            User::BASIC_GROUP . '.' . User::LAST_NAME => 'required|min:3|max:128',
            User::BASIC_GROUP . '.' . User::EMAIL => 'required|unique:users,email|min:2|max:64',
            User::BASIC_GROUP . '.' . User::USER_LITMOS_ID => 'nullable|min:3|max:128',
            User::BASIC_GROUP . '.' . User::OLD_USER_ID => 'nullable|min:3|max:128',
            User::BASIC_GROUP . '.' . User::WP_USER_ID => 'nullable|min:3|max:128',
            User::BASIC_GROUP . '.' . User::PASSWORD => 'required|min:6'
        ];
    }


}
