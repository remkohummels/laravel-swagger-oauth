<?php

namespace App\Http\Requests;

use App\Models\MetaType;
use App\Rules\Uuid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateMetaType
 * @package App\Http\Requests
 */
class UpdateMetaType extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $uuidRule = new Uuid();
        return [
            'id' => ['filled', $uuidRule],
            'name' => 'filled|min:2|max:128',
            'client_id' => 'nullable|exists:oauth_clients,id',
        ];
    }


}