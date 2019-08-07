<?php

namespace App\Http\Resources;

use App\Models\Company as CompanyModel;
use App\Models\CompanyData;
use App\Models\MetaType;
use Lcobucci\JWT\Parser;

/**
 * @OA\RequestBody(
 *     request="Company",
 *     description="Company request",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/Company")
 * )
 * @OA\Schema(
 *     description="Company model",
 *     type="object",
 *     title="Company model"
 * )
 * @OA\Schema(
 *     schema="ExistingCompany",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Company")
 *     }
 * )
 *
 *  * @OA\Property(
 *     property="___basic___",
 *     type="object",
 *     @OA\Property(
 *         property="name", type="string", format="", nullable=false, description="The name",
 *         minLength=3, maxLength=128, example="Example of a company name"
 *     ),
 *     @OA\Property(
 *         property="address", type="string", format="", nullable=false, description="The address",
 *         minLength=3, maxLength=256, example="Example of an address"
 *     ),
 *     @OA\Property(
 *         property="phone", type="string", format="", nullable=true, description="The phone",
 *         minLength=3, maxLength=64, example="+123 (45) 678-90-12 (Manager)"
 *     ),
 *     @OA\Property(property="zip", type="number", format="int32", nullable=true, description="The zip",
 *         minimum=0, maximum=1000000, example="123"
 *     ),
 *     @OA\Property(
 *         property="fax", type="string", format="", nullable=true, description="The fax",
 *         minLength=3, maxLength=64, example="Example of a fax"
 *     ),
 *     @OA\Property(
 *         property="website", type="string", format="hostname", nullable=true, description="The website",
 *         minLength=3, maxLength=128, example="https://colorelephant.com"
 *     ),
 *     @OA\Property(
 *         property="language", type="string", format="", nullable=true, description="The language",
 *         minLength=2, maxLength=64, example="Example of a language"
 *     ),
 *     @OA\Property(
 *         property="location", type="string", format="", nullable=true, description="The location",
 *         minLength=3, maxLength=64, example="Example of a location"
 *     ),
 *     @OA\Property(
 *         property="description", type="string", format="", nullable=true, description="The description",
 *         minLength=3, maxLength=1024, example="Example of a description"
 *     ),
 *     @OA\Property(
 *         property="short_description", type="string", format="", nullable=true, description="The short description",
 *         minLength=3, maxLength=512, example="Example of a short description"
 *     ),
 *     @OA\Property(property="opened_24_hours", type="boolean", description="Is it opened 24 hours", example=true),
 *     @OA\Property(
 *         property="payment_method", type="string", format="", nullable=true, description="The payment method",
 *         minLength=3, maxLength=128, example="Example of a payment method"
 *     ),
 *     @OA\Property(
 *         property="facebook_url", type="string", format="uri", nullable=true, description="The facebook url",
 *         minLength=3, maxLength=128, example="Example of a facebook url"
 *     ),
 *     @OA\Property(
 *         property="key_person_name", type="string", format="", nullable=true, description="The key person name",
 *         minLength=3, maxLength=128, example="Example of a key person name"
 *     ),
 *     @OA\Property(
 *         property="key_person_title", type="string", format="", nullable=true, description="The key person title",
 *         minLength=3, maxLength=128, example="Example of a key person title"
 *     ),
 *     @OA\Property(
 *         property="key_person_phone", type="string", format="", nullable=true, description="The key person phone",
 *         minLength=3, maxLength=128, example="Example of a key person phone"
 *     ),
 *     @OA\Property(
 *         property="key_person_email", type="string", format="", nullable=true, description="The key person email",
 *         minLength=3, maxLength=128, example="Example of a key person email"
 *     ),
 *     @OA\Property(
 *         property="eligibility_requirement", type="string", format="", nullable=true, description="The eligibility requirement",
 *         minLength=3, maxLength=512, example="Example of a eligibility requirement"
 *     )
 * )
 */
class Company extends BasicResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');
        $metaTypes = MetaType::where(MetaType::TYPE, '=', MetaType::TYPE_COMPANY_DATA)
            ->where(function ($query) use ($clientId) {
                $query->whereNull(MetaType::CLIENT_ID)
                    ->orWhere(MetaType::CLIENT_ID, '=', $clientId);
            })
            ->get([MetaType::NAME]);
        $metaTypeNames = collect($metaTypes->toArray())->flatten();

        $result = [CompanyModel::BASIC_GROUP => []];
        foreach ($this->resource->toArray() as $key => $item) {
            if ($metaTypeNames->contains($key)) {
                $result[$key] = array_diff_key($item->toArray(), array_flip(CompanyData::REQUIRED_FIELDS));
            } else {
                $result[CompanyModel::BASIC_GROUP][$key] = $item;
            }
        }

        return $result;
    }
}