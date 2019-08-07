<?php

namespace App\Http\Resources;

/**
 * Class MetaTypeCollection
 * @package App\Http\Resources
 */
class MetaTypeCollection extends BasicCollection
{


    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }


}