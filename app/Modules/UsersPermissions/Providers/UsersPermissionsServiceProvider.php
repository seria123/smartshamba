<?php

namespace App\Modules\UsersPermissions\Providers;

use Illuminate\Support\ServiceProvider;

class UsersPermissionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/access'), 'access');
    }
}
