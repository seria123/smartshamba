<?php

namespace App\Modules\Assets\Providers;

use Illuminate\Support\ServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/assets'), 'assets');
    }
}
