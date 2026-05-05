<?php

namespace App\Modules\Crops\Providers;

use Illuminate\Support\ServiceProvider;

class CropsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/crops'), 'crops');
    }
}
