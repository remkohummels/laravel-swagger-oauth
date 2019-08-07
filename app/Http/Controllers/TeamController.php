<?php

namespace App\Http\Controllers;

use App\Database\Eloquent\TeamQueryBuilder;
use App\Http\Requests\StoreTeam;
use App\Http\Requests\UpdateTeam;
use App\Http\Resources\TeamCollection;
use App\Models\Team;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Laratrust\Laratrust;

/**
 * Class TeamController
 * @package App\Http\Controllers
 */
class TeamController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/teams",
     *     summary="The list of teams",
     *     tags={"team"},
     *     description="Get the list of teams",
     *     operationId="listTeams",
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ExistingTeam")
     *         ),
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/team_includes"),
     *     @OA\Parameter(ref="#/components/parameters/team_sorts"),
     *     @OA\Parameter(ref="#/components/parameters/team_fields"),
     *     @OA\Parameter(ref="#/components/parameters/team_filters"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_TEAMS}}}
     * )
     *
     * @param TeamQueryBuilder $queryBuilder
     * @return TeamCollection
     */
    public function index(TeamQueryBuilder $queryBuilder)
    {
        return new TeamCollection($queryBuilder->paginate());
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
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/teams",
     *     tags={"team"},
     *     summary="Add a new team to the application",
     *     operationId="storeTeam",
     *     @OA\Response(
     *         response=201, description="team created",
     *         @OA\JsonContent(ref="#/components/schemas/Team")
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/Team"),
     *     security={{"passport": {L5_SAGGER_MANAGE_TEAMS}}}
     * )
     *
     * @param  StoreTeam  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTeam $request)
    {
        //
    }


    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/teams/{id}",
     *     summary="Find team by id",
     *     tags={"team"},
     *     description="Returns a singe team",
     *     operationId="showTeam",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingTeam")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_TEAMS}}}
     * )
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/teams/{id}",
     *     tags={"team"},
     *     summary="Updatge an existing team",
     *     operationId="updateTeam",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="team updated",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingTeam")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/Team"),
     *     security={{"passport": {L5_SAGGER_MANAGE_TEAMS}}}
     * )
     *
     * @param  UpdateTeam  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeam $request, Team $team)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/teams/{id}",
     *     summary="Deletes a team",
     *     tags={"team"},
     *     description="Deletes a singe team",
     *     operationId="deleteTeam",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(response=204, ref="#/components/responses/204"),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_MANAGE_TEAMS}}}
     * )
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        //
    }


    /**
     * @OA\Put(
     *     path="/teams/{id}/users/{user_id}",
     *     tags={"team"},
     *     summary="Add the user to the team",
     *     operationId="addUserToTeam",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Parameter(
     *         parameter="user_id",
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="The user added to the team"
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/Team"),
     *     security={{"passport": {L5_SAGGER_MANAGE_TEAMS}}}
     * )
     *
     * @param Team $team
     * @param User $user
     * @param Laratrust $laratrust
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function addUser(Team $team, User $user, Laratrust $laratrust)
    {
        $this->ownsOrHasRole(AuthServiceProvider::ROLE_ADMINISTRATOR, $team->company, $laratrust);

        $user->attachRole(AuthServiceProvider::ROLE_STAFF, $team);
        return response(['User assigned'], 204);
    }

    /**
     * @OA\Delete(
     *     path="/teams/{id}/users/{user_id}",
     *     summary="Remove the user from the team",
     *     tags={"team"},
     *     description="Remove the user from the team",
     *     operationId="removeUserFromTeam",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Parameter(
     *         parameter="user_id",
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="The user removed from the team",
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_MANAGE_TEAMS}}}
     * )
     *
     * @param Team $team
     * @param User $user
     * @param Laratrust $laratrust
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function removeUser(Team $team, User $user, Laratrust $laratrust)
    {
        $this->ownsOrHasRole(AuthServiceProvider::ROLE_ADMINISTRATOR, $team->company, $laratrust);

        $user->detachRole(AuthServiceProvider::ROLE_STAFF, $team);
        return response(['User detached'], 204);
    }


}
