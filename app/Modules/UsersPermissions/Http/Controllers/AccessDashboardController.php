<?php

namespace App\Modules\UsersPermissions\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\UsersPermissions\Models\Permission;
use App\Modules\UsersPermissions\Models\Role;
use Illuminate\Contracts\View\View;

class AccessDashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('access::dashboard', [
            'counts' => [
                'Users' => User::count(),
                'Roles' => Role::count(),
                'Permissions' => Permission::count(),
            ],
        ]);
    }
}
