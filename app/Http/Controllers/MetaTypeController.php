<?php

namespace App\Http\Controllers;

use App\Database\Eloquent\MetaTypeQueryBuilder;
use App\Http\Requests\StoreMetaType;
use App\Http\Requests\UpdateMetaType;
use App\Http\Resources\MetaType as MetaTypeResource;
use App\Http\Resources\MetaTypeCollection;
use App\Models\MetaType;

/**
 * Class MetaTypeController
 * @package App\Http\Controllers
 */
class MetaTypeController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/meta-types",
     *     summary="The list of meta types",
     *     tags={"meta type"},
     *     description="Get the list of meta types",
     *     operationId="listMetaTypes",
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ExistingMetaType")
     *         ),
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/meta_type_includes"),
     *     @OA\Parameter(ref="#/components/parameters/meta_type_sorts"),
     *     @OA\Parameter(ref="#/components/parameters/meta_type_fields"),
     *     @OA\Parameter(ref="#/components/parameters/meta_type_filters"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_META_TYPES}}}
     * )
     *
     * @param MetaTypeQueryBuilder $queryBuilder
     * @return MetaTypeCollection
     */
    public function index(MetaTypeQueryBuilder $queryBuilder)
    {
        return new MetaTypeCollection($queryBuilder->paginate());
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * @OA\Post(
     *     path="/meta-types",
     *     tags={"meta type"},
     *     summary="Add a new meta type to the application",
     *     operationId="storeMetaType",
     *     @OA\Response(
     *         response=201, description="meta type created",
     *         @OA\JsonContent(ref="#/components/schemas/MetaType")
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/MetaType"),
     *     security={{"passport": {L5_SAGGER_MANAGE_META_TYPES}}}
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param  StoreMetaType  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMetaType $request)
    {
        $type = new MetaType($request->validated());
        $type->save();

        return response(new MetaTypeResource($type),201)
            ->header('Location', route('meta-types.show', $type->id));
    }


    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/meta-types/{id}",
     *     summary="Find meta Type by id",
     *     tags={"meta type"},
     *     description="Returns a singe meta Type",
     *     operationId="showMetaType",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingMetaType")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_META_TYPES}}}
     * )
     *
     * @param  \App\Models\MetaType  $metaType
     * @return MetaTypeResource
     */
    public function show(MetaType $metaType)
    {
        return new MetaTypeResource($metaType);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MetaType  $metaType
     * @return \Illuminate\Http\Response
     */
    public function edit(MetaType $metaType)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/meta-types/{id}",
     *     tags={"meta type"},
     *     summary="Updatge an existing meta type",
     *     operationId="updateMetaType",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="meta type updated",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingMetaType")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/MetaType"),
     *     security={{"passport": {L5_SAGGER_MANAGE_META_TYPES}}}
     * )
     *
     * @param  UpdateMetaType  $request
     * @param  \App\Models\MetaType  $metaType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMetaType $request, MetaType $metaType)
    {
        $metaType->update($request->validated());
        return response(new MetaTypeResource($metaType));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/meta-types/{id}",
     *     summary="Deletes a meta type",
     *     tags={"meta type"},
     *     description="Deletes a singe meta type",
     *     operationId="deleteMetaType",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(response=204, ref="#/components/responses/204"),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_MANAGE_META_TYPES}}}
     * )
     *
     * @param  \App\Models\MetaType  $metaType
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(MetaType $metaType)
    {
        $metaType->delete();
        return response(null, 204);
    }


}
