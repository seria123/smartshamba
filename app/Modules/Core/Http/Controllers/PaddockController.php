<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Controllers\Concerns\ManagesCoreAssets;
use App\Modules\Core\Models\Paddock;

class PaddockController
{
    use ManagesCoreAssets;

    protected function modelClass(): string
    {
        return Paddock::class;
    }

    protected function routeBase(): string
    {
        return 'admin.core.paddocks';
    }

    protected function label(): string
    {
        return 'Paddock';
    }

    protected function allowsArea(): bool
    {
        return true;
    }
}
