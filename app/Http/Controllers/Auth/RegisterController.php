<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\StoreApplicationUserDataObject;
use App\Http\Requests\StoreStandardUserDataObject;
use App\Http\Requests\StoreUser;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Traits\SavesUserData;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Laratrust\Laratrust;
use Lcobucci\JWT\Parser;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, SavesUserData;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     tags={"user"},
     *     description="Signup endpoint",
     *     operationId="registerApi",
     *     @OA\RequestBody(
     *         description="Request with user credentials",
     *         required=true,
     *         @OA\JsonContent(
     *         @OA\Property(
     *             property="___basic___",
     *             type="object",
     *                 @OA\Property(
     *                    property="name", type="string", format="", nullable=false, description="Full name",
     *                     minLength=2, maxLength=64, example="i.e. John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="first_name", type="string", format="", nullable=true, description="The first name",
     *                     minLength=3, maxLength=128, example="Example of a first name"
     *                 ),
     *                 @OA\Property(
     *                     property="last_name", type="string", format="", nullable=true, description="The last name",
     *                     minLength=3, maxLength=128, example="Example of a last name"
     *                 ),
     *                 @OA\Property(
     *                     property="email", type="string", format="email", nullable=false, description="Email of a user",
     *                     minLength=3, maxLength=128, example="example@gmail.com"
     *                 ),
     *                 @OA\Property(
     *                     property="password", type="string", format="", nullable=false, description="Password",
     *                     minLength=6, maxLength=64, example="*********"
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation", type="string", format="", nullable=false, description="Password confirmation",
     *                     minLength=6, maxLength=64, example="*********"
     *                )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Users with such email already exists"
     *     ),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={}
     * )
     * @param Request $request
     * @param StoreStandardUserDataObject $standardRequest
     * @param StoreApplicationUserDataObject $applicationRequest
     * @return \Illuminate\Http\Response
     */
    public function registerApi(Request $request, StoreStandardUserDataObject $standardRequest,
                                StoreApplicationUserDataObject $applicationRequest)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()){
            return response($validator->errors()->first(), 422);
        }

        if ($request->bearerToken() != null) {
            $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');
        } else {
            $clientId = 2;
        }

        $user = $this->saveUser($validator->validated(), $standardRequest->validated(), $applicationRequest->validated(), (int)$clientId);

        event(new Registered($user));

        return response(['message' => 'User registered successfully']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            User::BASIC_GROUP . '.' . User::NAME       => ['required', 'string', 'max:128'],
            User::BASIC_GROUP . '.' . User::FIRST_NAME => ['required', 'string', 'max:128'],
            User::BASIC_GROUP . '.' . User::LAST_NAME  => ['required', 'string', 'max:128'],
            User::BASIC_GROUP . '.' . User::EMAIL      => ['required', 'string', 'email', 'max:64', 'unique:users,email'],
            User::BASIC_GROUP . '.' . User::PASSWORD   => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            User::NAME       => $data[User::BASIC_GROUP]['name'],
            User::FIRST_NAME => $data[User::BASIC_GROUP]['first_name'],
            User::LAST_NAME  => $data[User::BASIC_GROUP]['last_name'],
            User::EMAIL      => $data[User::BASIC_GROUP]['email'],
            User::PASSWORD   => Hash::make($data[User::BASIC_GROUP]['password']),
        ]);
    }
}
