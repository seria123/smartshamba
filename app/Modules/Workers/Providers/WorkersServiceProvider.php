<?php

namespace App\Modules\Workers\Providers;

use Illuminate\Support\ServiceProvider;

class WorkersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/labour'), 'labour');
    }
}
