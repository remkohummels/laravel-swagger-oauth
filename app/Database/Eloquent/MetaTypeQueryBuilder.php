<?php

namespace App\Database\Eloquent;

use App\Models\MetaType;
use Illuminate\Support\Collection;

/**
 * Class MetaTypeQueryBuilder
 *
 * @OA\Parameter(
 *     parameter="meta_type_includes", name="include", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_META_TYPE_INCLUDES, description="Comma separated list of related entities to include in results"
 * )
 * @OA\Parameter(
 *     parameter="meta_type_fields", name="fields", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_META_TYPE_FIELDS, description="Comma separated list of fields to get in results"
 * )
 * @OA\Parameter(
 *     parameter="meta_type_sorts", name="sort", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_META_TYPE_SORTS, description="Comma separated list of fields to sort result by"
 * )
 * @OA\Parameter(
 *     parameter="meta_type_filters", name="filter", in="query", required=false, description="Filter results by field value",
 *     @OA\Schema(
 *         type="object",
 *         @OA\Property(property="filter[name]", type="string", nullable=true, description="Filter by name", example="examp"),
 *         @OA\Property(property="filter[client_id]", type="string", nullable=true, description="Filter by company id", example="xxx-xxx"),
 *         @OA\Property(property="filter[type]", type="string", nullable=true, description="Type. 0 for user data, 1 for company data", example="0")
 *     )
 * )
 *
 * @package App\Database\Eloquent
 */
class MetaTypeQueryBuilder extends RestListQueryBuilder
{

    protected const MODEL_CLASS = MetaType::class;

    public const ALLOWED_SORTS = [
        MetaType::NAME,
    ];


    /**
     * @return Collection
     */
    protected static function getFieldsModels(): Collection
    {
        return collect(
            [
                'meta_types'  => new MetaType
            ]
        );
    }


    /**
     * @return array
     */
    protected static function getFilters(): array
    {
        return [
            MetaType::NAME,
            Filter::exact(MetaType::CLIENT_ID),
            Filter::exact(MetaType::TYPE)
        ];
    }


}
