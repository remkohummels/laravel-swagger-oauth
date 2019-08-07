<?php

namespace App\Http\Resources;

/**
 * @OA\RequestBody(
 *     request="MetaType",
 *     description="Meta type request",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/MetaType")
 * )
 * @OA\Schema(
 *     description="Meta type model",
 *     type="object",
 *     title="Meta type model"
 * )
 * @OA\Schema(
 *     schema="ExistingMetaType",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/MetaType"),
 *         @OA\Schema(@OA\Property(property="id", type="string"))
 *     }
 * )
 *
 * @OA\Property(
 *     property="name", type="string", format="", nullable=false, description="The name",
 *     minLength=1, maxLength=128, example="Example meta name"
 * )
 * @OA\Property(
 *     property="client_id", type="string", format="", nullable=false, description="Id of a client if data is private",
 *     minLength=1, maxLength=128, example="123"
 * )
 * @OA\Property(property="type", type="number", format="int32", nullable=false, description="The type",
 *     minimum=0, maximum=2, example="1"
 * )
 */
class MetaType extends BasicResource
{
}