<?php

namespace App\Http\Requests;

use App\Models\MetaType;

/**
 * Class StoreStandardCompanyDataObject
 * @package App\Http\Requests
 */
class StoreStandardCompanyDataObject extends DynamicMetaFormRequest
{
    protected function getMetaTypes(int $clientId)
    {
        return MetaType::where(MetaType::IS_CUSTOM, '=', MetaType::IS_STANDARD)
            ->where(MetaType::TYPE, '=', MetaType::TYPE_COMPANY_DATA)
            ->get();
    }
}