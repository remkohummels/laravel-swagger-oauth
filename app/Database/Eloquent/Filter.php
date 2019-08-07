<?php

namespace App\Database\Eloquent;

use App\Database\Eloquent\Filters\Filter as CustomFilter;
use App\Database\Eloquent\Filters\FiltersExact;
use App\Database\Eloquent\Filters\FiltersPartial;
use App\Database\Eloquent\Filters\FiltersScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 * @package App\Database\Eloquent
 */
class Filter
{
    /** @var string */
    protected $filterClass;

    /** @var string */
    protected $property;

    /** @var string */
    protected $columnName;


    /**
     * Filter constructor.
     * @param string $property
     * @param $filterClass
     * @param null $columnName
     */
    public function __construct(string $property, $filterClass, $columnName = null)
    {
        $this->property = $property;

        $this->filterClass = $filterClass;

        $this->columnName = $columnName ?? $property;
    }

    /**
     * @param string $property
     * @param null $columnName
     * @return Filter
     */
    public static function exact(string $property, $columnName = null) : self
    {
        return new static($property, FiltersExact::class, $columnName);
    }

    /**
     * @param string $property
     * @param null $columnName
     * @return Filter
     */
    public static function partial(string $property, $columnName = null) : self
    {
        return new static($property, FiltersPartial::class, $columnName);
    }

    /**
     * @param string $property
     * @param null $columnName
     * @return Filter
     */
    public static function scope(string $property, $columnName = null) : self
    {
        return new static($property, FiltersScope::class, $columnName);
    }

    /**
     * @param string $property
     * @param $filterClass
     * @param null $columnName
     * @return Filter
     */
    public static function custom(string $property, $filterClass, $columnName = null) : self
    {
        return new static($property, $filterClass, $columnName);
    }

    /**
     * @param Builder $builder
     * @param $value
     */
    public function filter(Builder $builder, $value)
    {
        $filterClass = $this->resolveFilterClass();

        ($filterClass)($builder, $value, $this->columnName);
    }

    /**
     * @return CustomFilter
     */
    private function resolveFilterClass(): CustomFilter
    {
        if ($this->filterClass instanceof CustomFilter) {
            return $this->filterClass;
        }

        return new $this->filterClass;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function isForProperty(string $property): bool
    {
        return $this->property === $property;
    }

    /**
     * @return string
     */
    public function getcolumnName(): string
    {
        return $this->columnName;
    }

}
