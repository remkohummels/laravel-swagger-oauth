<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFAController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api']);
    }

    public function get2FACode(Request $request)
    {
        $qrCodeURL = $request->user()->get2FACode();

        return response(['url' => $qrCodeURL]);
    }

    public function enable2FA(Request $request)
    {
        return $request->user()->enable2FA();
    }

    public function disable2FA(Request $request)
    {
        return $request->user()->disable2FA();
    }

    public function verifySecret(Request $request)
    {
        $valid = $request->user()->verify2FASecret($request);
        if ($valid) {
            return response(['message' => 'Secret is valid'], 200);
        }

        return response(['message' => 'Secret is invalid'], 400);
    }
}
