<?php

namespace App\Providers;

use App\Models\Pod;
use Illuminate\Support\Facades\View;
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
        // Make the sidebar pod list available to the dashboard layout,
        // scoped to whatever pods the logged-in user is allowed to see.
        View::composer('layouts.pods', function ($view) {
            $user = request()->user();

            $view->with('navPods', $user
                ? Pod::visibleTo($user)->orderBy('id')->get()
                : collect());
        });
    }
}
