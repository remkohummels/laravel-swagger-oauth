<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PasswordAuthentication
{
    public function passwordAPILogin(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendAPILoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function sendAPILoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticatedUser($request, $this->guard()->user());
    }

    protected function authenticatedUser(Request $request, $user)
    {
        $tokenResult = $user->createToken('Password Grant Token | User : '.$user->id);

        return response()->json([
            'user' => $user,
            'tokenType' => 'Bearer',
            'token' => $tokenResult->accessToken,
        ]);
    }
}
