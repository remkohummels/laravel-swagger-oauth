<?php

namespace App\Database\Eloquent\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface Filter
 * @package App\Database\Eloquent\Filters
 */
interface Filter
{


    /**
     * @param Builder $query
     * @param $value
     * @param string $property
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property) : Builder;


}
