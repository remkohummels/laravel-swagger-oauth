<?php

namespace App\Http\Requests;

use App\Models\MetaType;

/**
 * Class StoreStandardUserDataObject
 * @package App\Http\Requests
 */
class StoreStandardUserDataObject extends DynamicMetaFormRequest
{
    protected function getMetaTypes(int $clientId)
    {
        return MetaType::where(MetaType::IS_CUSTOM, '=', MetaType::IS_STANDARD)
            ->where(MetaType::TYPE, '=', MetaType::TYPE_USER_DATA)
            ->get();
    }
}