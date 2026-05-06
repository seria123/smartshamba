<?php

namespace App\Modules\Livestock\Providers;

use Illuminate\Support\ServiceProvider;

class LivestockServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/livestock'), 'livestock');
    }
}
