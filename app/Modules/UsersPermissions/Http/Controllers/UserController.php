<?php

namespace App\Modules\UsersPermissions\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('access::users.index', [
            'users' => User::query()
                ->with('memberships.organization', 'memberships.farm', 'memberships.role')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('access::users.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data): void {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'status' => $data['status'],
            ]);

            $this->syncMembership($user, $data);
        });

        return redirect()->route('access.users.index')->with('status', 'User created.');
    }

    public function show(User $user): View
    {
        return view('access::users.show', [
            'user' => $user->load('memberships.organization', 'memberships.farm', 'memberships.role.permissions'),
        ]);
    }

    public function edit(User $user): View
    {
        return view('access::users.form', $this->formData($user));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        DB::transaction(function () use ($data, $user): void {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'status' => $data['status'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = $data['password'];
            }

            $user->update($payload);
            $this->syncMembership($user, $data);
        });

        return redirect()->route('access.users.show', $user)->with('status', 'User updated.');
    }

    public function deactivate(User $user): RedirectResponse
    {
        $user->update(['status' => 'inactive']);

        return redirect()->route('access.users.show', $user)->with('status', 'User deactivated.');
    }

    private function formData(?User $user = null): array
    {
        return [
            'user' => $user?->load('memberships'),
            'membership' => $user?->memberships->first(),
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'roles' => Role::query()->orderBy('name')->get(),
        ];
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['nullable', 'integer', Rule::exists('farms', 'id')],
            'role_id' => ['required', 'integer', Rule::exists('roles', 'id')],
        ];

        $data = $request->validate($rules);

        if (! empty($data['farm_id'])) {
            $farm = Farm::query()->findOrFail($data['farm_id']);
            abort_unless((int) $farm->organization_id === (int) $data['organization_id'], 422);
        }

        return $data;
    }

    private function syncMembership(User $user, array $data): void
    {
        $role = Role::query()->findOrFail($data['role_id']);

        OrganizationMembership::query()->updateOrCreate(
            [
                'organization_id' => $data['organization_id'],
                'user_id' => $user->id,
            ],
            [
                'role_id' => $role->id,
                'role_key' => $role->key,
                'farm_id' => $data['farm_id'] ?? null,
                'status' => 'active',
                'joined_at' => now(),
            ],
        );
    }
}
