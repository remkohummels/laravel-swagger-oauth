<?php

namespace App\Http\Requests;

use App\Models\MetaType;

/**
 * Class UpdateStandardUserDataObject
 * @package App\Http\Requests
 */
class UpdateApplicationUserDataObject extends StoreApplicationUserDataObject
{
    const REQUIRED_RULE = 'filled';
}