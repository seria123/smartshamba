<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class CustomDashboard extends BaseDashboard
{
    protected static bool $shouldRegisterNavigation = false;

    public function mount()
    {
        return redirect()->route('admin.dashboard');
    }
}
