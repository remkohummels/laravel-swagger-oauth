<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUser
 * @package App\Http\Requests
 */
class UpdateUser extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $id = $this->route('user')->id;
        return [
            User::BASIC_GROUP . '.' . User::NAME => 'filled|min:2|max:64',
            User::BASIC_GROUP . '.' . User::FIRST_NAME => 'filled|min:3|max:128',
            User::BASIC_GROUP . '.' . User::LAST_NAME => 'filled|min:3|max:128',
            User::BASIC_GROUP . '.' . User::EMAIL => 'filled|unique:users,email,' . $id . '|min:2|max:64',
            User::BASIC_GROUP . '.' . User::USER_LITMOS_ID => 'nullable|min:3|max:128',
            User::BASIC_GROUP . '.' . User::OLD_USER_ID=> 'nullable|min:3|max:128',
            User::BASIC_GROUP . '.' . User::WP_USER_ID => 'nullable|min:3|max:128',
            User::BASIC_GROUP . '.' . User::PASSWORD => 'filled|min:6'
        ];
    }


}
