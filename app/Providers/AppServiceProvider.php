<?php

namespace App\Providers;


use App\Models\Location;
use App\Models\Script;
use App\Observers\LocationObserver;
use App\Observers\ScriptObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        Location::observe(LocationObserver::class);
        Script::observe(ScriptObserver::class);
    }
}
