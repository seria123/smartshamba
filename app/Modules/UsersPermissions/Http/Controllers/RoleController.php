<?php

namespace App\Modules\UsersPermissions\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\UsersPermissions\Models\Role;
use Illuminate\Contracts\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('access::roles.index', [
            'roles' => Role::query()->withCount('permissions')->orderBy('name')->get(),
        ]);
    }

    public function show(Role $role): View
    {
        return view('access::roles.show', [
            'role' => $role->load('permissions'),
        ]);
    }
}
