<?php

namespace App\Database\Eloquent\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class FiltersPartial
 * @package App\Database\Eloquent\Filters
 */
class FiltersPartial implements Filter
{


    /**
     * @param Builder $query
     * @param $value
     * @param string $property
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $wrappedProperty = $query->getQuery()->getGrammar()->wrap($property);

        $sql = "LOWER({$wrappedProperty}) LIKE ?";

        if (is_array($value)) {
            return $query->where(function (Builder $query) use ($value, $sql) {
                foreach ($value as $partialValue) {
                    $partialValue = mb_strtolower($partialValue, 'UTF8');

                    $query->orWhereRaw($sql, ["%{$partialValue}%"]);
                }
            });
        }

        $value = mb_strtolower($value, 'UTF8');

        return $query->whereRaw($sql, ["%{$value}%"]);
    }


}
