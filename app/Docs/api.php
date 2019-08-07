<?php
/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="0.1",
 *         description="Coffective API",
 *         title="Coffective API",
 *         termsOfService=L5_SWAGGER_CONST_TERMS_OF_SERVICES,
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         ),
 *         @OA\Contact(
 *             email="black@colorelephant.com"
 *         )
 *     ),
 *     @OA\Server(
 *         description=L5_SWAGGER_CONST_ENV,
 *         url=L5_SWAGGER_CONST_HOST
 *     ),
 *     @OA\ExternalDocumentation(
 *         description="Who built this?",
 *         url="http://colorelephant.com"
 *     ),
 *     @OA\Components(
 *         @OA\Parameter(
 *             parameter="id_in_path_required",
 *             name="id",
 *             in="path",
 *             required=true,
 *             description="The ID of the item",
 *             @OA\Schema(type="string")
 *         ),
 *         @OA\Parameter(
 *             parameter="include_in_query",
 *             name="include",
 *             in="query",
 *             required=false,
 *             description="Include related entities to the responce",
 *             @OA\Schema(type="string")
 *         ),
 *         @OA\Parameter(
 *             parameter="page",
 *             name="page",
 *             in="query",
 *             required=false,
 *             description="Page of the list",
 *             @OA\Schema(type="string")
 *         ),
 *         @OA\Response(
 *             response=200,
 *             description="Successful operation"
 *         ),
 *         @OA\Response(
 *             response=204,
 *             description="Entity deleted"
 *         ),
 *         @OA\Response(
 *             response=401,
 *             description="Unauthorized"
 *         ),
 *         @OA\Response(
 *             response=403,
 *             description="Forbidden"
 *         ),
 *         @OA\Response(
 *             response=404,
 *             description="Entity not found"
 *         ),
 *         @OA\Response(
 *             response=405,
 *             description="Method not allowed"
 *         ),
 *         @OA\Response(
 *             response=410,
 *             description="Gone"
 *         ),
 *         @OA\Response(
 *             response=415,
 *             description="Unsupported Media Type"
 *         ),
 *         @OA\Response(
 *             response=422,
 *             description="Validation error"
 *         ),
 *         @OA\Response(
 *             response=429,
 *             description="Too many requests"
 *         )
 *     )
 * )
 */