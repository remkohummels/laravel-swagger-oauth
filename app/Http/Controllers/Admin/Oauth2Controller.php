<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\PasswordAuthentication;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class Oauth2Controller extends Controller
{
    use AuthenticatesUsers, PasswordAuthentication;

    public function __construct()
    {
        $this->middleware('auth')->only('index');
    }

    public function index(Request $request)
    {
        return view('oauth-dashboard.index');
    }

    public function callback(Request $request)
    {
        dd($request->code);
    }
}
