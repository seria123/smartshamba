<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Models\ModuleRegistry;
use Illuminate\Contracts\View\View;

class ModuleRegistryController extends Controller
{
    public function index(): View
    {
        return view('core::modules.index', [
            'modules' => ModuleRegistry::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }
}
