<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
    }

    /**
     * @OA\Post(
     *     path="/password/reset",
     *     summary="Resets the password of the currently logged in user",
     *     tags={"password"},
     *     description="Resets the password of the currently logged in user",
     *     operationId="resetPassword",
     *     @OA\RequestBody(
     *         description="Request for resetting current user's password",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="password", type="string", format="", nullable=false, description="Old password",
     *                 minLength=3, maxLength=128, example="********"
     *             ),
     *             @OA\Property(
     *                 property="new_password", type="string", format="", nullable=false, description="New password",
     *                 minLength=3, maxLength=128, example="********"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error happened while resetting"
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/401"),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {L5_SAGGER_READ_USERS}}}
     * )
     * @param Request $request
     */
    public function resetLoggedIn(Request $request)
    {
        $user = $this->guard()->user();

        $credentials = [
            'email' => $user->email,
            'password' => $request->get('password')
        ];

        if (Auth::guard('web')->validate($credentials)) {
            $user->password = Hash::make($request->get('new_password'));
            $user->setRememberToken(Str::random(60));
            $user->save();

            event(new PasswordReset($user));

            $user->tokens()->each(function($token) {$token->revoke();});

            Passport::pruneRevokedTokens();

            return response(['message' => trans(Password::PASSWORD_RESET)]);
        } else {
            return response(['message' => trans(Password::INVALID_USER)], 422);
        }
    }

    /**
     * @override method
     *
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();

        event(new PasswordReset($user));
    }

}
