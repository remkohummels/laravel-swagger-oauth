<?php

namespace App\Http\Controllers;

use App\Database\Eloquent\UserQueryBuilder;
use App\Http\Requests\StoreUser;
use App\Http\Requests\StoreStandardUserDataObject;
use App\Http\Requests\StoreApplicationUserDataObject;
use App\Http\Requests\UpdateApplicationUserDataObject;
use App\Http\Requests\UpdateStandardUserDataObject;
use App\Http\Requests\UpdateUser;
use App\Http\Resources\UserFull as UserFullResource;
use App\Http\Resources\UserCollection;
use App\Models\User;
use App\Models\UserData;
use App\Providers\AuthServiceProvider;
use App\Traits\SavesUserData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;
use Laratrust\Laratrust;
use Lcobucci\JWT\Parser;

/**
 * Class UserController.
 */
class UserController extends Controller
{

    use SavesUserData;

    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/users",
     *     summary="The list of users",
     *     tags={"user"},
     *     description="Get the list of users",
     *     operationId="listUsers",
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ExistingUser")
     *         ),
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/user_includes"),
     *     @OA\Parameter(ref="#/components/parameters/user_sorts"),
     *     @OA\Parameter(ref="#/components/parameters/user_fields"),
     *     @OA\Parameter(ref="#/components/parameters/user_filters"),
     *     @OA\Parameter(ref="#/components/parameters/page"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_USERS}}}
     * )
     *
     * @param UserQueryBuilder $queryBuilder
     * @return UserCollection
     */
    public function index(UserQueryBuilder $queryBuilder)
    {
        return new UserCollection($queryBuilder->paginate());
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }


    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/users",
     *     tags={"user"},
     *     summary="Add a new user to the application",
     *     operationId="storeUser",
     *     @OA\Response(
     *         response=201, description="user created",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/User"),
     *     security={{"passport": {L5_SAGGER_MANAGE_USERS}}}
     * )
     *
     * @param StoreUser $request
     * @param StoreStandardUserDataObject $standardRequest
     * @param StoreApplicationUserDataObject $applicationRequest
     * @param Laratrust $laratrust
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUser $request, StoreStandardUserDataObject $standardRequest,
                          StoreApplicationUserDataObject $applicationRequest, Laratrust $laratrust)
    {
        $this->hasRole(AuthServiceProvider::ROLE_ADMINISTRATOR, $laratrust);

        $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');
        $user = $this->saveUser($request->validated(), $standardRequest->validated(), $applicationRequest->validated(), (int)$clientId);

        event(new Registered($user));

        return response(new UserFullResource($user), 201)
            ->header('Location', route('users.show', $user->id));
    }


    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/current-user",
     *     summary="Get data of a current logged in user",
     *     tags={"user"},
     *     description="Returns the current logged in user",
     *     operationId="currentUser",
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingUser")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_USERS}}}
     * )
     *
     * @return UserFullResource
     */
    public function current()
    {
        $user = auth()->user();
        $userFullResource = new UserFullResource($user);
        $userFullResource['roles'] = $user->roles;
        return $userFullResource;
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Find user by id",
     *     tags={"user"},
     *     description="Returns a singe user",
     *     operationId="showUser",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingUser")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_USERS}}}
     * )
     *
     * @param \App\Models\User $user
     *
     * @return UserFullResource
     */
    public function show(User $user)
    {
        return new UserFullResource($user);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
    }


    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"user"},
     *     summary="Updatge an existing user",
     *     operationId="updateUser",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(
     *         response=200, description="user updated",
     *         @OA\JsonContent(ref="#/components/schemas/ExistingUser")
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=422, ref="#/components/responses/422"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     @OA\RequestBody(ref="#/components/requestBodies/User"),
     *     security={{"passport": {L5_SAGGER_MANAGE_USERS}}}
     * )
     *
     * @param UpdateUser $request
     * @param User       $user
     * @param Laratrust $laratrust
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request, UpdateStandardUserDataObject $standardRequest,
                           UpdateApplicationUserDataObject $applicationRequest, User $user, Laratrust $laratrust)
    {
//        return response(['message' => $standardRequest->validated()]);

        $this->ownsOrHasRole(AuthServiceProvider::ROLE_ADMINISTRATOR, $user, $laratrust, 'id');

        $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');
        $this->saveUser($request->validated(), $standardRequest->validated(), $applicationRequest->validated(), (int)$clientId, $user);

        return response(new UserFullResource($user));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Deletes a user",
     *     tags={"user"},
     *     description="Deletes a singe user",
     *     operationId="deleteUser",
     *     @OA\Parameter(ref="#/components/parameters/id_in_path_required"),
     *     @OA\Response(response=204, ref="#/components/responses/204"),
     *     @OA\Response(response=404, ref="#/components/responses/404"),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_MANAGE_USERS}}}
     * )
     *
     * @param \App\Models\User $user
     * @param Laratrust $laratrust
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(User $user, Laratrust $laratrust)
    {
        $this->ownsOrHasRole(AuthServiceProvider::ROLE_ADMINISTRATOR, $user, $laratrust, 'id');

        if (empty($user->applicationData)) {
            Log::error($user->id . ' user was an orphan. No application data found in NoSql. The system will delete a user.');
        } else {
            $user->applicationData->delete();
        }

        if (empty($user->defaultData)) {
            Log::error($user->id . ' user was an orphan. No default data found in NoSql. The system will delete a user.');
        } else {
            $user->defaultData->delete();
        }

        $user->delete();

        return response(null, 204);
    }


}
