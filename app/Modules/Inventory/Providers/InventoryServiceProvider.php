<?php

namespace App\Modules\Inventory\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')->group(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(resource_path('views/inventory'), 'inventory');
    }
}
