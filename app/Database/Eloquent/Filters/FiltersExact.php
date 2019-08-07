<?php

namespace App\Database\Eloquent\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class FiltersExact
 * @package App\Database\Eloquent\Filters
 */
class FiltersExact implements Filter
{


    /**
     * @param Builder $query
     * @param $value
     * @param string $property
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        if (is_array($value)) {
            return $query->whereIn($property, $value);
        }

        return $query->where($property, $value);
    }


}
