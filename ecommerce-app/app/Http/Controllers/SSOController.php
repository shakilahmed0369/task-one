<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class SSOController extends Controller
{

    public function redirectToSSO(Request $request)
    {

        $state = Str::random(40);

        session(['state' => $state]);

        $query = http_build_query([

            'client_id' => config('app.sso_client_id'),
            'redirect_uri' => config('app.sso_redirect_uri'),
            'response_type' => 'code',
            'scope' => '',
            'state' => $state

        ]);

        return redirect(config('app.sso_server') . '/oauth/authorize?' . $query);
    }


    public function callback(Request $request)
    {

        if ($request->state != session('state')) {
            abort(403);
        }

        $response = Http::asForm()->post(config('app.sso_server') . '/oauth/token', [

            'grant_type' => 'authorization_code',
            'client_id' => config('app.sso_client_id'),
            'client_secret' => config('app.sso_client_secret'),
            'redirect_uri' => config('app.sso_redirect_uri'),
            'code' => $request->code

        ]);

        $token = $response->json();

        $userResponse = Http::withToken($token['access_token'])
            ->get(config('app.sso_server') . '/api/user');

        $ssoUser = $userResponse->json();

        $user = User::updateOrCreate(

            ['email' => $ssoUser['email']],

            [
                'name' => $ssoUser['name'],
                'password' => bcrypt(Str::random(16))
            ]

        );

        Auth::login($user);

        return redirect('/dashboard');
    }
}
