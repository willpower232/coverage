<?php

namespace App\Providers;

use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // is this necessary?
        // $this->app->bind('path.public', function () {
        //     return base_path('public_html');
        // });
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function (Request $request) {
            $token = $this->getTokenForRequest($request);

            if ($token != config('coverage.auth_token')) {
                return null;
            }

            return new GenericUser([]);
        });
    }

    /**
     * borrowed from Illuminate\Auth\TokenGuard@getTokenForRequest.
     */
    private function getTokenForRequest(Request $request): ?string
    {
        $token = $request->query('auth_token');

        if (empty($token)) {
            $token = $request->input('auth_token');
        }

        if (empty($token)) {
            $token = $request->bearerToken();
        }

        // not sure this can be tested
        // if (empty($token)) {
        //     $token = $request->getPassword();
        // }

        return $token;
    }
}
