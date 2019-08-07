<?php

namespace App\Http\Requests;

use App\Models\MetaType;

/**
 * Class UpdateApplicationCompanyDataObject
 * @package App\Http\Requests
 */
class UpdateApplicationCompanyDataObject extends StoreApplicationCompanyDataObject
{
    const REQUIRED_RULE = 'filled';
}