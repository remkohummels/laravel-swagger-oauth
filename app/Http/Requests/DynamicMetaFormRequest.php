<?php

namespace App\Http\Requests;

use App\Models\BuildingObject;
use App\Models\MetaType;
use App\Models\ObjectType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DynamicObjectFormRequest
 * @package App\Http\Requests
 */
abstract class DynamicMetaFormRequest extends FormRequest
{
    const REQUIRED_RULE = 'required';

    /** @var array $rules */
    protected $rules = [];


    /**
     * Loads validation rules from object meta scheme
     * @param int $clientId
     */
    public function initializeRules(int $clientId): void
    {
        $metaTypes = $this->getMetaTypes($clientId);
        if (empty($metaTypes) === false) {
            foreach ($metaTypes as $metaType) {
                $this->rules[$metaType->name] = $this->getValidationString($metaType);
            }
        }
    }

    abstract protected function getMetaTypes(int $clientId);

    /**
     * Builds Laravel validation string
     *
     * @param MetaType $metaType
     * @return string
     */
    protected function getValidationString(MetaType $metaType): string
    {
        // Data will not be saved if no validation defined
        return 'nullable';

        // TODO if validation needed, we should implement submeta fields.
        // https://gitlab.com/color-elephant/building-objects/blob/master/app/Http/Requests/DynamicChildObjectsFormRequest.php

        /*
        $validation = collect();

        if ($metaType->is_required) {
            $validation->push(static::REQUIRED_RULE);
        }

        if (empty($metaType->validation) === false) {
            $validation = $validation->merge(explode('|', $metaType->validation));
        }

        if ($validation->isEmpty()) {
            // Data will not be saved if no validation defined
            $validation->push('nullable');
        }

        return $validation->implode('|');
        */
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->rules;
    }


}