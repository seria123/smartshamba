<?php

namespace App\Modules\Finance\Providers;

use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/finance'), 'finance');
    }
}
