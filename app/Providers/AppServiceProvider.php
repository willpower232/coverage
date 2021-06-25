<?php

namespace App\Providers;

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
}
