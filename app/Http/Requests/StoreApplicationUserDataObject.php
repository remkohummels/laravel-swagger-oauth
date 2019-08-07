<?php

namespace App\Http\Requests;

use App\Models\MetaType;

/**
 * Class StoreStandardUserDataObject
 * @package App\Http\Requests
 */
class StoreApplicationUserDataObject extends DynamicMetaFormRequest
{
    protected function getMetaTypes(int $clientId)
    {
        return MetaType::where(MetaType::IS_CUSTOM, '=', MetaType::IS_APP_SPECIFIC)
            ->where(MetaType::TYPE, '=', MetaType::TYPE_USER_DATA)
            ->where(MetaType::CLIENT_ID, '=', $clientId)
            ->get();
    }
}