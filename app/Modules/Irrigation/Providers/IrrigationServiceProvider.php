<?php

namespace App\Modules\Irrigation\Providers;

use Illuminate\Support\ServiceProvider;

class IrrigationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/irrigation'), 'irrigation');
    }
}
