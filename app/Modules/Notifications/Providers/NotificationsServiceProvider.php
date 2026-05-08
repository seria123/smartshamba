<?php

namespace App\Modules\Notifications\Providers;

use Illuminate\Support\ServiceProvider;

class NotificationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');
        $this->loadViewsFrom(resource_path('views/notifications'), 'notifications');
    }
}
