<?php

namespace App\Providers;


use App\Models\Film;
use App\Policies\FilmPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    protected $policies = [
        Film::class => FilmPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin', function ($user) {
            return $user->isAdmin()
                ? \Illuminate\Auth\Access\Response::allow()
                : \Illuminate\Auth\Access\Response::deny('Only admins can perform this action.');
        });
    }

    
}
