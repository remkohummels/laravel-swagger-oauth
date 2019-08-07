<?php

namespace App\Http\Requests;

use App\Models\MetaType;

/**
 * Class UpdateStandardCompanyDataObject
 * @package App\Http\Requests
 */
class UpdateStandardCompanyDataObject extends StoreStandardCompanyDataObject
{
    const REQUIRED_RULE = 'filled';
}