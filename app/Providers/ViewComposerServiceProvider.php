<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Http\ViewComposers\AdminComposer::class);
        $this->app->singleton(\App\Http\ViewComposers\LoginComposer::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer(['admin.auth.*'], 'App\Http\ViewComposers\LoginComposer');
        view()->composer(['admin.pages.*','admin.layouts.*'], 'App\Http\ViewComposers\AdminComposer');
    }
}
