<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * @OA\Post(
     *     path="/password/forgot",
     *     summary="Request for resetting forgotten password",
     *     tags={"password"},
     *     description="Returns result of sending email",
     *     operationId="forgotPassword",
     *     @OA\RequestBody(
     *         description="Request for resetting password",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="email", type="string", format="email", nullable=false, description="Email of a user",
     *                 minLength=3, maxLength=128, example="example@gmail.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Users with such email not found"
     *     ),
     *     @OA\Response(response=403, ref="#/components/responses/403"),
     *     @OA\Response(response=405, ref="#/components/responses/405"),
     *     @OA\Response(response=410, ref="#/components/responses/410"),
     *     @OA\Response(response=429, ref="#/components/responses/429"),
     *     security={{"passport": {}}}
     * )
     * @param Request $request
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        if ($request->is('api/*')) {
            return ['message' => trans($response)];
        } else {
            return back()->with('status', trans($response));
        }
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        if ($request->is('api/*')) {
            return new Response(['message' => trans($response)], 422);
        } else {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($response)]);
        }
    }
}
