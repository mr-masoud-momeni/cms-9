<?php

//developer comment: this class run a class called "UserDataComposer".


namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\UserDataComposer;

class ShareDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (!request()->is('admin/*') && !request()->is('api/*')) {
            View::composer(['Frontend.*'], UserDataComposer::class);
        }

    }
}
