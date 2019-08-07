<?php

namespace App\Database\Eloquent;

use App\Database\Eloquent\Exceptions\InvalidFieldQuery;
use App\Models\Company;
use App\Models\MetaType;
use App\Models\Team;
use App\Models\User;
use App\Models\UserData;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class UserQueryBuilder
 *
 * @OA\Parameter(
 *     parameter="user_includes", name="include", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_USER_INCLUDES, description="Comma separated list of related entities to include in results"
 * )
 * @OA\Parameter(
 *     parameter="user_fields", name="fields", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_USER_FIELDS, description="Comma separated list of fields to get in results"
 * )
 * @OA\Parameter(
 *     parameter="user_sorts", name="sort", in="query", required=false, @OA\Schema(type="string"),
 *     example=L5_SWAGGER_USER_SORTS, description="Comma separated list of fields to sort result by"
 * )
 * @OA\Parameter(
 *     parameter="user_filters", name="filter", in="query", required=false, description="Filter results by field value",
 *     @OA\Schema(
 *         type="object",
 *         @OA\Property(property="filter[name]", type="string", nullable=true, description="Filter by name", example="examp"),
 *         @OA\Property(property="filter[email]", type="string", nullable=true, description="Filter by email", example="example@ex.com")
 *     )
 * )
 *
 * @package App\Database\Eloquent
 */
class UserQueryBuilder extends AbstractQueryBuilder
{

    protected const MODEL_CLASS = User::class;

    public const ALLOWED_SORTS = [
        User::NAME,
        User::EMAIL,
    ];


    public function setMetaTypeQueryBuilder()
    {
        $this->metaTypeQueryBuilder = MetaType::where(MetaType::TYPE, '=', MetaType::TYPE_USER_DATA);
    }

    /**
     * @return Collection
     */
    protected static function getFieldsModels(): Collection
    {
        return collect(
            [
                'users' => new User,
                'teams' => new Team,
            ]
        );
    }


    /**
     * @return array
     */
    protected static function getFilters(): array
    {
        return [
            User::NAME,
            Filter::exact(User::EMAIL),
        ];
    }

    protected function addMetasToResult(Collection $result, $entityIds, $joinMetaTypes, $joinMetaTypeFields)
    {
        $metaDatas = UserData::whereIn(UserData::USER_ID, $entityIds)
            ->whereIn(UserData::NAME, $joinMetaTypes)->get();

        $result->map(
            function ($item) use ($metaDatas, $joinMetaTypeFields) {
                $metaData = $metaDatas->filter(
                    function ($meta) use ($item) {
                        $entityId = UserData::USER_ID;
                        return $meta->$entityId === $item->id;
                    }
                );

                foreach ($metaData as $meta) {
                    $name = $meta[UserData::NAME];
                    $meta->offsetUnset(UserData::NAME);
                    if (!empty($joinMetaTypeFields[$name])) {
                        foreach ($meta->getAttributes() as $attr => $attrValue) {
                            if (!in_array($attr, $joinMetaTypeFields[$name])) {
                                $meta->offsetUnset($attr);
                            }
                        }
                    }
                    $item->$name = $meta;
                }

                return $item;
            }
        );

        return $result;
    }

}
