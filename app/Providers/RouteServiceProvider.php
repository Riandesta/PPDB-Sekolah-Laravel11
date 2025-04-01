<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvicer extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    public const HOME = '/dashboard';
    public const ADMIN_HOME = '/admin/dashboard';
    public const STAFF_HOME = '/staff/dashboard';
}
