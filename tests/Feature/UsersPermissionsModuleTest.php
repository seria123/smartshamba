<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\CoreFoundationSeeder;
use Database\Seeders\UsersPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersPermissionsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Log in');
    }

    public function test_valid_user_can_log_in_and_out(): void
    {
        $this->seedAccess();

        $this->post(route('login'), [
            'email' => 'admin@smartshamba.test',
            'password' => 'password',
        ])->assertRedirect(route('core.dashboard'));

        $this->assertAuthenticated();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guest_cannot_access_admin_core(): void
    {
        $this->get(route('core.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_admin_access(): void
    {
        $this->get(route('access.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_core_and_access(): void
    {
        $this->seedAccess();
        $admin = User::query()->where('email', 'admin@smartshamba.test')->firstOrFail();

        $this->actingAs($admin);

        $this->get(route('core.dashboard'))->assertOk();
        $this->get(route('access.dashboard'))->assertOk();
    }

    public function test_default_roles_and_permissions_are_seeded(): void
    {
        $this->seedAccess();

        foreach ([
            'owner',
            'system-admin',
            'farm-manager',
            'agronomist',
            'livestock-officer',
            'storekeeper',
            'finance-officer',
            'farm-hand',
            'contractor',
            'auditor',
        ] as $roleKey) {
            $this->assertDatabaseHas('roles', ['key' => $roleKey]);
        }

        foreach (['core.view', 'core.manage', 'access.view', 'access.manage', 'modules.view', 'modules.manage', 'reports.view', 'reports.manage'] as $permissionKey) {
            $this->assertDatabaseHas('permissions', ['key' => $permissionKey]);
        }

        $systemAdmin = Role::query()->where('key', 'system-admin')->firstOrFail();
        $this->assertTrue($systemAdmin->permissions()->where('key', 'access.manage')->exists());
    }

    public function test_user_can_be_assigned_membership_and_role(): void
    {
        $this->seedAccess();
        $admin = User::query()->where('email', 'admin@smartshamba.test')->firstOrFail();
        $organization = Organization::query()->firstOrFail();
        $farm = Farm::query()->firstOrFail();
        $role = Role::query()->where('key', 'farm-manager')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('access.users.store'), [
                'name' => 'New Farm Manager',
                'email' => 'manager@smartshamba.test',
                'password' => 'password123',
                'status' => 'active',
                'organization_id' => $organization->id,
                'farm_id' => $farm->id,
                'role_id' => $role->id,
            ])
            ->assertRedirect(route('access.users.index'));

        $user = User::query()->where('email', 'manager@smartshamba.test')->firstOrFail();

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'farm_id' => $farm->id,
            'role_id' => $role->id,
            'role_key' => 'farm-manager',
        ]);
    }

    public function test_non_admin_user_cannot_access_access_management_pages(): void
    {
        $this->seedAccess();
        $organization = Organization::query()->firstOrFail();
        $role = Role::query()->where('key', 'farm-hand')->firstOrFail();
        $user = User::query()->create([
            'name' => 'Farm Hand',
            'email' => 'hand@smartshamba.test',
            'password' => 'password123',
            'status' => 'active',
        ]);

        OrganizationMembership::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => $role->id,
            'role_key' => $role->key,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('access.dashboard'))
            ->assertForbidden();
    }

    private function seedAccess(): void
    {
        $this->seed(CoreFoundationSeeder::class);
        $this->seed(UsersPermissionsSeeder::class);
    }
}
