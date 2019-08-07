<?php

namespace App\Http\Requests;

use App\Models\MetaType;
use App\Rules\Uuid;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreMetaType
 * @package App\Http\Requests
 */
class StoreMetaType extends FormRequest
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
            'name' => 'required|min:2|max:128',
            'client_id' => 'nullable|exists:oauth_clients,id',
        ];
    }


}