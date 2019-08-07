<?php

namespace App\Database\Eloquent;

use App\Database\Eloquent\Exceptions\InvalidAppendQuery;
use App\Database\Eloquent\Exceptions\InvalidFieldQuery;
use App\Database\Eloquent\Exceptions\InvalidFilterQuery;
use App\Database\Eloquent\Exceptions\InvalidIncludeQuery;
use App\Database\Eloquent\Exceptions\InvalidSortQuery;
use App\Models\BasicEntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class QueryBuilder
 * @package App\Database\Eloquent
 */
class RestListQueryBuilder extends Builder
{
    protected const MODEL_CLASS = null;

    public const ALLOWED_SORTS = [];

    /** @var \Illuminate\Support\Collection */
    protected $allowedFilters;

    /** @var \Illuminate\Support\Collection */
    protected $allowedFields;

    /** @var string|null */
    protected $defaultSort;

    /** @var \Illuminate\Support\Collection */
    protected $allowedSorts;

    /** @var \Illuminate\Support\Collection */
    protected $allowedIncludes;

    /** @var \Illuminate\Support\Collection */
    protected $allowedAppends;

    /** @var \Illuminate\Support\Collection */
    protected $fields;

    /** @var \Illuminate\Support\Collection */
    protected $appends;

    /** @var Request */
    protected $request;


    /**
     * QueryBuilder constructor.
     * @param Builder $builder
     * @param Request|null $request
     */
    public function __construct(Builder $builder, ? Request $request = null)
    {
        $modelClass  = static::MODEL_CLASS;
        $modelObject = new $modelClass;
        $builder->setModel($modelObject);

        parent::__construct(clone $builder->getQuery());

        $this->initializeFromBuilder($builder);

        $this->request = $request ?? request();

        $this->initializeRequest();

        if ($this->request->fields()->isNotEmpty()) {
            $this->parseSelectedFields();
        }

        if ($this->request->sorts()->isNotEmpty()) {
            $this->allowedSorts('*');
        }

        $this->initializeParameters();
    }

    /**
     * Add the model, scopes, eager loaded relationships, local macro's and onDelete callback
     * from the $builder to this query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     */
    protected function initializeFromBuilder(Builder $builder)
    {
        $this->setModel($builder->getModel())
            ->setEagerLoads($builder->getEagerLoads());

        $builder->macro(
            'getProtected',
            function (Builder $builder, string $property) {
                return $builder->{$property};
            }
        );

        $this->scopes = $builder->getProtected('scopes');

        $this->localMacros = $builder->getProtected('localMacros');

        $this->onDelete = $builder->getProtected('onDelete');
    }

    /**
     *
     */
    protected function initializeRequest()
    {
        $this->request->macro(
            'includes',
            function ($include = null) {
                $parameter    = 'include';
                $includeParts = $this->query($parameter);
                if (! is_array($includeParts)) {
                    $includeParts = explode(',', strtolower($this->query($parameter)));
                }

                $includes = collect($includeParts)->filter();
                if (is_null($include)) {
                    return $includes;
                }

                return $includes->contains(strtolower($include));
            }
        );

        $this->request->macro(
            'appends',
            function ($append = null) {
                $parameter   = 'append';
                $appendParts = $this->query($parameter);
                if (! is_array($appendParts)) {
                    $appendParts = explode(',', strtolower($this->query($parameter)));
                }

                $appends = collect($appendParts)->filter();
                if (is_null($append)) {
                    return $appends;
                }

                return $appends->contains(strtolower($append));
            }
        );

        $this->request->macro(
            'filters',
            function ($filter = null) {
                $filterParts = $this->query('filter', []);
                if (is_string($filterParts)) {
                    return collect();
                }

                $filters       = collect($filterParts);
                $filtersMapper = function ($value) {
                    if (is_array($value)) {
                        return collect($value)->map($this->bindTo($this))->all();
                    }

                    if (str_contains($value, ',')) {
                        return explode(',', $value);
                    }

                    if ($value === 'true') {
                        return true;
                    }

                    if ($value === 'false') {
                        return false;
                    }

                    return $value;
                };

                $filters = $filters->map($filtersMapper->bindTo($filtersMapper));
                if (is_null($filter)) {
                    return $filters;
                }

                return $filters->get(strtolower($filter));
            }
        );

        $this->request->macro(
            'fields',
            function () {
                $fieldsParam = $this->query('fields');
                if (empty($fieldsParam)) {
                    return collect();
                }

                $fields = collect(explode(',', $fieldsParam));
                return collect(
                    $fields->mapToGroups(
                        function ($item) {
                            preg_match('/((?<table>.*)\.)?(?<field>.*)/', $item, $matches);
                            return [$matches['table'] => $matches['field']];
                        }
                    )->toArray()
                );

            }
        );

        $this->request->macro(
            'sort',
            function ($default = null) {
                return $this->query('sort', $default);
            }
        );

        $this->request->macro(
            'sorts',
            function ($default = null) {
                $sortParts = $this->sort();
                if (! is_array($sortParts)) {
                    $sortParts = explode(',', $sortParts);
                }

                $sorts = collect($sortParts)->filter();
                if ($sorts->isNotEmpty()) {
                    return $sorts;
                }

                return collect($default)->filter();
            }
        );

    }

    protected function parseSelectedFields()
    {
        $modelTableName = $this->getModel()->getTable();

        /** @var Collection $fields */
        $fields = $this->request->fields();
        if ($fields->has('')) {
            $fields->offsetSet($modelTableName, $fields->get(''));
            $fields->offsetUnset('');
        }

        $this->fields = $fields;
        $modelFields  = $this->fields->get($modelTableName, ['*']);
        // Primary key is used for joining
        $primaryKey = $this->getModel()->getKeyName();
        array_push($modelFields, $primaryKey);

        $this->select($this->prependFieldsWithTableName($modelFields, $modelTableName));
    }

    /**
     * @param array $fields
     * @param string $tableName
     * @return array
     */
    protected function prependFieldsWithTableName(array $fields, string $tableName): array
    {
        return array_map(
            function ($field) use ($tableName) {
                return "{$tableName}.{$field}";
            },
            $fields
        );
    }

    /**
     * @param $sorts
     * @return self
     */
    public function allowedSorts($sorts) : self
    {
        $sorts = is_array($sorts) ? $sorts : func_get_args();
        if (! $this->request->sorts()) {
            return $this;
        }

        $this->allowedSorts = collect($sorts);

        if (! $this->allowedSorts->contains('*')) {
            $this->guardAgainstUnknownSorts();
        }

        $this->addSortsToQuery($this->request->sorts($this->defaultSort));

        return $this;
    }

    /**
     *
     */
    protected function guardAgainstUnknownSorts()
    {
        /** @var Collection $sorts */
        $sorts = $this->request->sorts()->map(
            function ($sort) {
                return ltrim($sort, '-');
            }
        );

        $diff = $sorts->diff($this->allowedSorts);

        if ($diff->count()) {
            throw InvalidSortQuery::sortsNotAllowed($diff, $this->allowedSorts);
        }
    }

    /**
     * @param Collection $sorts
     */
    protected function addSortsToQuery(Collection $sorts)
    {
        $this->filterDuplicates($sorts)
            ->each(
                function (string $sort) {
                    $descending = $sort[0] === '-';

                    $key = ltrim($sort, '-');

                    $this->orderBy($key, ($descending) ? 'desc' : 'asc');
                }
            );
    }

    /**
     * @param Collection $sorts
     * @return Collection
     */
    protected function filterDuplicates(Collection $sorts): Collection
    {
        $orders = $this->getQuery()->orders;
        if (!is_array($orders)) {
            return $sorts;
        }

        return $sorts->reject(
            function (string $sort) use ($orders) {
                $toSort = [
                    'column' => ltrim($sort, '-'),
                    'direction' => ($sort[0] === '-') ? 'desc' : 'asc',
                ];

                foreach ($orders as $order) {
                    if ($order === $toSort) {
                        return true;
                    }
                }

                return null;
            }
        );
    }

    protected function initializeParameters(): void
    {
        $this->allowedIncludes(static::getIncludes()->all());
        $this->allowedFields($this->getFieldsWithForeignKeys());
        $this->allowedFilters(static::getFilters());
        $this->allowedSorts(static::ALLOWED_SORTS);
    }

    /**
     * @param $includes
     * @return self
     */
    public function allowedIncludes($includes) : self
    {
        $includes = is_array($includes) ? $includes : func_get_args();

        $this->allowedIncludes = collect($includes)
            ->flatMap(
                function ($include) {
                    return collect(explode('.', $include))
                        ->reduce(
                            function (Collection $collection, $include) {
                                if ($collection->isEmpty()) {
                                    return $collection->push($include);
                                }

                                return $collection->push("{$collection->last()}.{$include}");
                            },
                            collect()
                        );
                }
            );

        $this->guardAgainstUnknownIncludes();

        $this->addIncludesToQuery($this->request->includes());

        return $this;
    }

    /**
     *
     */
    protected function guardAgainstUnknownIncludes()
    {
        /** @var Collection $includes */
        $includes = $this->request->includes();

        $diff = $includes->diff($this->allowedIncludes);

        if ($diff->count()) {
            throw InvalidIncludeQuery::includesNotAllowed($diff, $this->allowedIncludes);
        }
    }

    /**
     * @param Collection $includes
     */
    protected function addIncludesToQuery(Collection $includes)
    {
        $includes
            ->map('camel_case')
            ->map(
                function (string $include) {
                    return collect(explode('.', $include));
                }
            )
            ->flatMap(
                function (Collection $relatedTables) {
                    return $relatedTables
                        ->mapWithKeys(
                            function ($table, $key) use ($relatedTables) {
                                $fullRelationName = $relatedTables->slice(0, $key + 1)->implode('.');
                                $fields = $this->getFieldsForRelatedTable($fullRelationName);
                                if (empty($fields)) {
                                    return [$fullRelationName];
                                }

                                return [$fullRelationName => function (Relation $query) use ($fullRelationName, $fields) {
                                    $foreignKeys     = $this->getForeignKeys($fullRelationName);
                                    $qualifiedFields = $this->prependFieldsWithTableName($fields, $query->getModel()->getTable());
                                    $query->select($foreignKeys->merge($qualifiedFields)->unique()->all());
                                }];
                            }
                        );
                }
            )
            ->pipe(
                function (Collection $withs) {
                    $this->with($withs->all());
                }
            );
    }

    /**
     * @param string $relation
     * @return array
     */
    protected function getFieldsForRelatedTable(string $relation): array
    {
        if (! $this->fields) {
            return ['*'];
        }

        return $this->fields->get(snake_case($relation), []);
    }

    /**
     * @return Collection
     */
    protected static function getIncludes(): Collection
    {
        return static::getFieldsModels()->except(static::getMainEntityTableName())->keys();
    }

    /**
     * @return Collection
     */
    protected static function getFieldsModels(): Collection
    {
        return collect();
    }

    /**
     * @return string
     */
    protected static function getMainEntityTableName(): string
    {
        return Str::lower(Str::plural(Str::snake(class_basename(static::MODEL_CLASS))));
    }

    /**
     * @param $fields
     * @return self
     */
    public function allowedFields($fields) : self
    {
        $fields = is_array($fields) ? $fields : func_get_args();

        $this->allowedFields = collect($fields)
            ->map(
                function (string $fieldName) {
                    if (!str_contains($fieldName, '.')) {
                        $modelTableName = $this->getModel()->getTable();

                        return "{$modelTableName}.{$fieldName}";
                    }

                    return $fieldName;
                }
            );

        if (! $this->allowedFields->contains('*')) {
            $this->guardAgainstUnknownFields();
        }

        return $this;
    }

    /**
     *
     */
    protected function guardAgainstUnknownFields()
    {
        if (empty($this->fields)) {
            return;
        }

        $fields = $this->fields
            ->map(
                function ($fields, $model) {
                    $tableName = snake_case(preg_replace('/-/', '_', $model));

                    $fields = array_map('snake_case', $fields);

                    return $this->prependFieldsWithTableName($fields, $tableName);
                }
            )
            ->flatten()
            ->unique();

        $diff = $fields->diff($this->allowedFields);

        if ($diff->count()) {
            throw InvalidFieldQuery::fieldsNotAllowed($diff, $this->allowedFields);
        }
    }


    /**
     * @return array
     */
    protected function getFieldsWithForeignKeys(): array
    {
        return $this->getForeignKeys()->merge(static::getFields())->all();
    }


    /**
     * @param bool $skipMainEntityTable
     * @return Collection
     */
    protected static function getFields($skipMainEntityTable = false): Collection
    {
        $getAllowedFields = function (Model $model, string $table) use ($skipMainEntityTable): Collection {
            $result = collect($model->getFillable())
                ->prepend(BasicEntityModel::ID)
                ->diff($model->getHidden());

            $tableDot = '';
            if ($skipMainEntityTable === false || static::getMainEntityTableName() !== $table) {
                $tableDot = $table . '.';
            }

            return $result->map(
                function ($field) use ($tableDot) {
                    return $tableDot . $field;
                }
            );
        };

        return  static::getFieldsModels()->map($getAllowedFields)->collapse();
    }


    /**
     * @param string $key
     * @return Collection
     */
    protected function getForeignKeys(string $key = null): Collection
    {
        $includes = static::getIncludes();
        if (empty($key) === false) {
            $includes = $includes->filter(
                function (string $item) use ($key) {
                    return $item === $key;
                }
            );
        }

        $keys = $includes->map(
            function(string $relationName) {
                $foreignKeys = collect();

                $relation = $this->getRelationFromModel($relationName);

                if ($relation instanceof HasOneOrMany || $relation instanceof HasManyThrough) {
                    $foreignKeys->push($relation->getQualifiedForeignKeyName());
                }

                return $foreignKeys->merge($this->getForeignKeysForKids($relationName, $relation));
            }
        );

        return $keys->collapse()->filter();
    }


    /**
     * @param string $relationName
     * @param Relation $relation
     * @return Collection
     */
    protected function getForeignKeysForKids(string $relationName, Relation $relation): Collection
    {
        $includes = static::getIncludes();

        $cameledRelation = Str::camel($relationName);
        return $includes->map(
            function(string $include) use ($cameledRelation, $relation) {
                if (Str::contains($include, '.' . $cameledRelation . '.')
                    || Str::startsWith($include, $cameledRelation . '.')
                ) {
                    if ($relation instanceof HasOneOrMany || $relation instanceof HasManyThrough) {
                        return $cameledRelation . '.' . $relation->getLocalKeyName();
                    } elseif ($relation instanceof BelongsToMany) {
                        return $cameledRelation . '.' . $relation->getRelatedKeyName();
                    } elseif ($relation instanceof BelongsTo) {
                        return $cameledRelation . '.' . $relation->getOwnerKey();
                    }
                }

                return null;
            }
        )->filter();
    }


    /**
     * @param string $relationName
     * @return mixed
     */
    protected function getRelationFromModel(string $relationName)
    {
        $cameledRelation = Str::camel($relationName);

        if (Str::contains($cameledRelation, '.')) {
            $relations       = collect(explode('.', $cameledRelation));
            $subRelationName = $relations->pop();
            $parentRelation  = $this->getModel();

            $relations->map(
                function ($subRelation) use (&$parentRelation, &$foreignKey) {
                    $parentRelation = $parentRelation->$subRelation()->getRelated();
                }
            );

            $relation = $parentRelation->$subRelationName();
        } else {
            $relation = $this->getModel()->$cameledRelation();
        }

        return $relation;
    }


    /**
     * @param Model $model
     * @param string $table
     * @return Collection
     */
    protected static function getAllowedFields(Model $model, string $table): Collection
    {
        $result = collect($model->getFillable())
            ->prepend(BasicEntityModel::ID)
            ->diff($model->getHidden());

        $tableDot = '';
        if (static::getMainEntityTableName() !== $table) {
            $tableDot = $table . '.';
        }

        return $result->map(
            function ($field) use ($tableDot) {
                return $tableDot . $field;
            }
        );
    }

    /**
     * @param $filters
     * @return self
     */
    public function allowedFilters($filters) : self
    {
        $filters = is_array($filters) ? $filters : func_get_args();
        $this->allowedFilters = collect($filters)->map(
            function ($filter) {
                if ($filter instanceof Filter) {
                    return $filter;
                }

                return Filter::partial($filter);
            }
        );

        $this->guardAgainstUnknownFilters();

        $this->addFiltersToQuery($this->request->filters());

        return $this;
    }

    /**
     *
     */
    protected function guardAgainstUnknownFilters()
    {
        /** @var Collection $filterNames */
        $filterNames = $this->request->filters()->keys();

        $allowedFilterNames = $this->allowedFilters->map->getProperty();

        $diff = $filterNames->diff($allowedFilterNames);

        if ($diff->count()) {
            throw InvalidFilterQuery::filtersNotAllowed($diff, $allowedFilterNames);
        }
    }

    /**
     * @param Collection $filters
     */
    protected function addFiltersToQuery(Collection $filters)
    {
        $filters->each(
            function ($value, $property) {
                $filter = $this->findFilter($property);

                $filter->filter($this, $value);
            }
        );
    }

    /**
     * @param string $property
     * @return Filter|null
     */
    protected function findFilter(string $property) : ? Filter
    {
        return $this->allowedFilters
            ->first(
                function (Filter $filter) use ($property) {
                    return $filter->isForProperty($property);
                }
            );
    }

    /**
     * Children should override this method to set filters
     */
    protected static function getFilters(): array
    {
        // Override it if you have filters
        return [];
    }

    /**
     * Create a new QueryBuilder for a request and model.
     *
     * @param string|\Illuminate\Database\Query\Builder $baseQuery Model class or base query builder
     * @param Request $request
     *
     * @return self
     */
    public static function for($baseQuery, ? Request $request = null) : self
    {
        if (is_string($baseQuery)) {
            /** @var \Illuminate\Database\Query\Builder $baseQuery */
            $baseQuery = ($baseQuery)::query();
        }

        return new static($baseQuery, $request ?? request());
    }

    /**
     * @return string
     */
    public static function getIncludesExample(): string
    {
        return static::getIncludes()->implode(',');
    }

    /**
     * @return string
     */
    public static function getFieldsExample(): string
    {
        return static::getFields(true)->implode(',');
    }

    /**
     * @return string
     */
    public static function getFiltersExample(): string
    {
        $filters = collect(static::getFilters())->map(
            function ($filter) {
                if (is_string($filter)) {
                    return 'filter[' . $filter . ']=examp';
                } elseif (is_a($filter, Filter::class)) {
                    /** @var Filter $filter */
                    return 'filter[' . $filter->getProperty() . ']=exact_value';
                }

                return null;
            }
        );

        return $filters->toJson();
    }

    /**
     * @return string
     */
    public static function getSortExample(): string
    {
        return implode(',', static::ALLOWED_SORTS);
    }

    /**
     * @param $sort
     * @return self
     */
    public function defaultSort($sort) : self
    {
        $this->defaultSort = $sort;

        $this->addSortsToQuery($this->request->sorts($this->defaultSort));

        return $this;
    }

    /**
     * @param $appends
     * @return self
     */
    public function allowedAppends($appends) : self
    {
        $appends = is_array($appends) ? $appends : func_get_args();

        $this->allowedAppends = collect($appends);

        $this->guardAgainstUnknownAppends();

        $this->appends = $this->request->appends();

        return $this;
    }

    protected function guardAgainstUnknownAppends()
    {
        /** @var Collection $appends */
        $appends = $this->request->appends();

        $diff = $appends->diff($this->allowedAppends);

        if ($diff->count()) {
            throw InvalidAppendQuery::appendsNotAllowed($diff, $this->allowedAppends);
        }
    }

    /**
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function get($columns = ['*'])
    {
        $result = parent::get($columns);

        if ($this->appends && $this->appends->count() > 0) {
            $result = $this->setAppendsToResult($result);
        }

        return $result;
    }

    /**
     * @param Collection $result
     * @return mixed
     */
    public function setAppendsToResult(Collection $result)
    {
        $result->map(
            function ($item) {
                $item->append($this->appends->toArray());

                return $item;
            }
        );

        return $result;
    }


}
