<?php

namespace App\Http\Requests;

use App\Models\MetaType;

/**
 * Class UpdateStandardUserDataObject
 * @package App\Http\Requests
 */
class UpdateStandardUserDataObject extends StoreStandardUserDataObject
{
    const REQUIRED_RULE = 'filled';
}