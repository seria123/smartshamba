<?php

namespace App\Modules\Core\Http\Controllers;

use App\Modules\Core\Http\Controllers\Concerns\ManagesCoreAssets;
use App\Modules\Core\Models\Field;

class FieldController
{
    use ManagesCoreAssets;

    protected function modelClass(): string
    {
        return Field::class;
    }

    protected function routeBase(): string
    {
        return 'admin.core.fields';
    }

    protected function label(): string
    {
        return 'Field';
    }

    protected function allowsArea(): bool
    {
        return true;
    }
}
