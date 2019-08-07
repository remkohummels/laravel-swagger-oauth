<?php
namespace App\Database\Eloquent;


use App\Models\MetaType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class AbstractQueryBuilder extends RestListQueryBuilder
{

    /** @var QueryBuilder */
    protected $metaTypeQueryBuilder;

    public function __construct(Builder $builder, ? Request $request = null)
    {
        $this->setMetaTypeQueryBuilder();
        return parent::__construct($builder, $request);
    }

    abstract protected function setMetaTypeQueryBuilder();

    /**
     * @param array $columns
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function get($columns = ['*'])
    {
        // Yeah, that's ugly =(
        $fields = $this->fields;
        $joinMetaTypes = [];
        $joinMetaTypeFields = [];

        if (empty($fields)) {
            $joinMetaTypes = $this->metaTypeQueryBuilder->pluck(MetaType::NAME);
        } else {
            /** @var Collection $metaTypes */
            $metaTypes = $this->metaTypeQueryBuilder
                ->pluck(MetaType::NAME)
                ->map(function ($item) {
                    return Str::before($item, '.');
                });

            $tableName = $this->getMainTableName();
            $exclude = [];
            foreach ($fields as $key => $column) {
                if ($metaTypes->contains($column) || $metaTypes->contains($key) || $metaTypes->intersect($column)) {
                    if (is_array($column)) {
                        if ($key === $tableName) {
                            $joinMetaTypes = array_merge($joinMetaTypes, $column);
                            foreach ($column as $aColumn) {
                                $exclude []= $tableName . '.' . $aColumn;
                            }
                        } else {
                            $joinMetaTypes []= $key;
                            $joinMetaTypeFields [$key]= $column;
                            $exclude []= $tableName . '.' . $key;
                        }
                    } else {
                        $joinMetaTypes []= $column;
                        $exclude []= $tableName . '.' . $column;
                    }
                }
            }

            $this->getQuery()->columns = array_diff(
                $this->getQuery()->columns,
                [$tableName . '.*'],
                $exclude
            );
        }


        $result = parent::get($columns);

        if ($this->appends && $this->appends->count() > 0) {
            $result = $this->setAppendsToResult($result);
        }

        if (empty($joinMetaTypes) === false) {
            $entityIds = collect($result->toArray())->flatten()->toArray();
            $result = $this->addMetasToResult($result, $entityIds, $joinMetaTypes, $joinMetaTypeFields);
        }

        return $result;
    }

    abstract protected function addMetasToResult(Collection $result, $entityIds, $joinMetaTypes, $joinMetaTypeFields);

    /**
     * @param bool $skipMainEntityTable
     * @return Collection
     */
    protected static function getFields($skipMainEntityTable = false): Collection
    {
        $fields = parent::getFields($skipMainEntityTable);

        if ($skipMainEntityTable === false) {
//            $metaTypes = $this->metaTypeQueryBuilder
//                ->pluck(MetaType::NAME)
//                ->map(functieon ($item) {
//                    return Str::snake($item);
//                });
//
//            $fields = $metaTypes->merge($fields);
        }

        return $fields;
    }

    protected function guardAgainstUnknownFields()
    {
        if (empty($this->fields)) {
            return;
        }

        $metaTypes = $this->metaTypeQueryBuilder->get();

        $fields = $this->fields
            ->map(
                function ($fields, $model) use ($metaTypes) {
                    $tableName = snake_case(preg_replace('/-/', '_', $model));
                    $types = $metaTypes->filter(function($item) use ($tableName) {return Str::snake($item->{MetaType::NAME}) === $tableName;});
                    if ($types->isNotEmpty()) {
                        return $this->prependFieldsWithTableName([$tableName], $this->getMainTableName());
                    } else {
                        $fields = array_map('snake_case', $fields);

                        return $this->prependFieldsWithTableName($fields, $tableName);
                    }
                }
            )
            ->flatten()
            ->unique();

        $diff = $fields->diff($this->allowedFields);

        if ($diff->count()) {
            throw InvalidFieldQuery::fieldsNotAllowed($diff, $this->allowedFields);
        }
    }


    protected function getMainTableName()
    {
        return Str::lower(Str::plural(class_basename(static::MODEL_CLASS)));
    }

}
