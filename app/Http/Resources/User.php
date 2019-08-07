<?php

namespace App\Http\Resources;

use App\Models\MetaType;
use App\Models\User as UserModel;
use App\Models\UserData;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Parser;

/**
 * @OA\RequestBody(
 *     request="User",
 *     description="User request",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/User")
 * )
 * @OA\Schema(
 *     description="User model",
 *     type="object",
 *     title="User model"
 * )
 * @OA\Schema(
 *     schema="ExistingUser",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/User")
 *     }
 * )
 *
 * @OA\Property(
 *     property="___basic___",
 *     type="object",
 *      @OA\Property(
 *         property="name", type="string", format="", nullable=false,
 *         minLength=2, maxLength=64, example="i.e. John Doe"
 *     ),
 *     @OA\Property(
 *         property="first_name", type="string", format="", nullable=true, description="The first name",
 *         minLength=3, maxLength=128, example="Example of a first name"
 *     ),
 *     @OA\Property(
 *         property="last_name", type="string", format="", nullable=true, description="The last name",
 *         minLength=3, maxLength=128, example="Example of a last name"
 *     ),
 *     @OA\Property(
 *         property="email", type="string", format="", nullable=false,
 *         maxLength=64, example="i.e. johndoe@example.com"
 *     ),
 *     @OA\Property(
 *         property="user_litmos_id", type="string", format="", nullable=true, description="The user litmos id",
 *         minLength=3, maxLength=128, example="Example of a user litmos id"
 *     ),
 *     @OA\Property(
 *         property="old_user_id", type="string", format="", nullable=true, description="The old user id",
 *         minLength=3, maxLength=128, example="Example of a old user id"
 *     ),
 *     @OA\Property(
 *         property="password", type="string", format="", nullable=false,
 *         minLength=6, maxLength=64, example="i.e. ****"
 *     )
 * )
 */
class User extends BasicResource
{


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');
        $metaTypes = MetaType::where(MetaType::TYPE, '=', MetaType::TYPE_USER_DATA)
            ->where(function ($query) use ($clientId) {
                $query->whereNull(MetaType::CLIENT_ID)
                    ->orWhere(MetaType::CLIENT_ID, '=', $clientId);
            })
            ->get([MetaType::NAME]);
        $metaTypeNames = collect($metaTypes->toArray())->flatten();

        $result = [UserModel::BASIC_GROUP => []];
        foreach ($this->resource->toArray() as $key => $item) {
            if ($metaTypeNames->contains($key)) {
                $result[$key] = array_diff_key($item->toArray(), array_flip(UserData::REQUIRED_FIELDS));
            } else {
                $result[UserModel::BASIC_GROUP][$key] = $item;
            }
        }

        return $result;
    }


}
