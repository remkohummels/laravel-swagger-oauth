<?php

namespace App\Http\Resources;

/**
 * @OA\RequestBody(
 *     request="Team",
 *     description="Team request",
 *     required=true,
 *     @OA\JsonContent(ref="#/components/schemas/Team")
 * )
 * @OA\Schema(
 *     description="Team model",
 *     type="object",
 *     title="Team model"
 * )
 * @OA\Schema(
 *     schema="ExistingTeam",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Team"),
 *         @OA\Schema(@OA\Property(property="id", type="string"))
 *     }
 * )
 */
class Team extends BasicResource
{
}
