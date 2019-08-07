<?php

namespace App\Database\Eloquent;

use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Class TeamQueryBuilder
 *
 * @OA\Parameter(
 *     parameter="team_includes", name="include", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_TEAM_INCLUDES, description="Comma separated list of related entities to include in results"
 * )
 * @OA\Parameter(
 *     parameter="team_fields", name="fields", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_TEAM_FIELDS, description="Comma separated list of fields to get in results"
 * )
 * @OA\Parameter(
 *     parameter="team_sorts", name="sort", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_TEAM_SORTS, description="Comma separated list of fields to sort result by"
 * )
 * @OA\Parameter(
 *     parameter="team_filters", name="filter", in="query", required=false, description="Filter results by field value",
 *     @OA\Schema(
 *         type="object",
 *         @OA\Property(property="filter[name]", type="string", nullable=true, description="Filter by name", example="examp"),
 *         @OA\Property(property="filter[company_id]", type="string", nullable=true, description="Filter by company id", example="xxx-xxx")
 *     )
 * )
 *
 * @package App\Database\Eloquent
 */
class TeamQueryBuilder extends RestListQueryBuilder
{

    protected const MODEL_CLASS = Team::class;

    public const ALLOWED_SORTS = [
        Team::NAME,
    ];


    /**
     * @return Collection
     */
    protected static function getFieldsModels(): Collection
    {
        return collect(
            [
                'teams'   => new Team,
                'company' => new Company,
                'users'   => new User,
            ]
        );
    }


    /**
     * @return array
     */
    protected static function getFilters(): array
    {
        return [
            Team::NAME,
            Filter::exact(Team::COMPANY_ID)
        ];
    }


}
