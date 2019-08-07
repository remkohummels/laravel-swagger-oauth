<?php

namespace App\Database\Eloquent;

use App\Models\Company;
use App\Models\CompanyData;
use App\Models\MetaType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Class CompanyQueryBuilder
 *
 * @OA\Parameter(
 *     parameter="company_includes", name="include", in="query", required=false, description="Comma separated list of related entities",
 *     example=L5_SWAGGER_COMPANY_INCLUDES, @OA\Schema(type="string")
 * ),
 * @OA\Parameter(
 *     parameter="company_fields", name="fields", in="query", required=false, description="Comma separated list of fields",
 *     example=L5_SWAGGER_COMPANY_FIELDS, @OA\Schema(type="string")
 * ),
 * @OA\Parameter(
 *     parameter="company_sorts", name="sort", in="query", required=false, description="Comma separated list of fields to sort by",
 *     example=L5_SWAGGER_COMPANY_SORTS, @OA\Schema(type="string")
 * ),
 * @OA\Parameter(
 *     parameter="company_filters", name="filter", in="query", required=false, description="Filter results by field value",
 *     @OA\Schema(
 *         type="object",
 *         @OA\Property(property="filter[name]", type="string", nullable=true, description="Filter by name", example="examp"),
 *         @OA\Property(
 *             property="filter[address]", type="string", nullable=true, description="Filter by address", example="examp"
 *         ),
 *         @OA\Property(property="filter[phone]", type="string", nullable=true, description="Filter by phone", example="examp")
 *     )
 * )
 *
 * @package App\Database\Eloquent
 */
class CompanyQueryBuilder extends AbstractQueryBuilder
{

    protected const MODEL_CLASS = Company::class;

    public const ALLOWED_SORTS = [
        Company::NAME,
        Company::ADDRESS,
        Company::ZIP,
        Company::PHONE,
        Company::LANGUAGE,
    ];


    public function setMetaTypeQueryBuilder()
    {
        $this->metaTypeQueryBuilder = MetaType::where(MetaType::TYPE, '=', MetaType::TYPE_COMPANY_DATA);
    }


    /**
     * @return Collection
     */
    protected static function getFieldsModels(): Collection
    {
        return collect(
            [
                'companies'   => new Company,
                'teams'       => new Team,
                'teams.users' => new User,
            ]
        );
    }


    /**
     * @return array
     */
    protected static function getFilters(): array
    {
        return [
            Company::NAME,
            Company::ADDRESS,
            Company::PHONE,
            Company::ZIP,
            Company::LANGUAGE,
        ];
    }

    protected function addMetasToResult(Collection $result, $entityIds, $joinMetaTypes, $joinMetaTypeFields)
    {
        $metaDatas = CompanyData::whereIn(CompanyData::COMPANY_ID, $entityIds)
            ->whereIn(CompanyData::NAME, $joinMetaTypes)->get();

        $result->map(
            function ($item) use ($metaDatas, $joinMetaTypeFields) {
                $metaData = $metaDatas->filter(
                    function ($meta) use ($item) {
                        $entityId = CompanyData::COMPANY_ID;
                        return $meta->$entityId === $item->id;
                    }
                );

                foreach ($metaData as $meta) {
                    $name = $meta[CompanyData::NAME];
                    $meta->offsetUnset(CompanyData::NAME);
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
