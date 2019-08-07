<?php

namespace App\Database\Eloquent\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class FiltersScope
 * @package App\Database\Eloquent\Filters
 */
class FiltersScope implements Filter
{


    /**
     * @param Builder $query
     * @param $values
     * @param string $property
     * @return Builder
     */
    public function __invoke(Builder $query, $values, string $property) : Builder
    {
        $scope = camel_case($property);
        $values = array_wrap($values);

        return $query->$scope(...$values);
    }


}
