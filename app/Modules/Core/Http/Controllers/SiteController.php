<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Controllers\Concerns\ManagesCoreAssets;
use App\Modules\Core\Models\Site;

class SiteController
{
    use ManagesCoreAssets;

    protected function modelClass(): string
    {
        return Site::class;
    }

    protected function routeBase(): string
    {
        return 'admin.core.sites';
    }

    protected function label(): string
    {
        return 'Site';
    }

    protected function allowsDescription(): bool
    {
        return true;
    }

    protected function allowsSite(): bool
    {
        return false;
    }
}
