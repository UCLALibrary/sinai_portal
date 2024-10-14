<?php

namespace App\Providers;


use App\Models\Location;
use App\Observers\LocationObserver;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
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
        Inertia::share('appUrl', config('app.url'));

		Location::observe(LocationObserver::class);
    }
}
