<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Documents\Models\AttachmentCategory;
use App\Modules\Documents\Models\FarmAttachment;
use App\Modules\UsersPermissions\Models\OrganizationMembership;
use App\Modules\UsersPermissions\Models\Role;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentsAttachmentsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_documents_dashboard(): void
    {
        $this->get('/admin/documents')->assertRedirect(route('login'));
    }

    public function test_user_without_documents_permission_is_denied(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs($this->memberWithRole('farm-hand'))->get(route('documents.dashboard'))->assertForbidden();
    }

    public function test_user_with_documents_view_can_access_dashboard_list_and_detail(): void
    {
        $user = $this->adminUser();
        $attachment = $this->attachment();

        $this->actingAs($user)->get(route('documents.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('documents.attachments.index'))->assertOk();
        $this->actingAs($user)->get(route('documents.attachments.show', $attachment))->assertOk();
    }

    public function test_user_with_documents_manage_can_upload_valid_attachment(): void
    {
        Storage::fake('local');
        $user = $this->adminUser();
        [$organization, $farm] = $this->scope();
        $category = AttachmentCategory::firstOrFail();

        $this->actingAs($user)->post(route('documents.attachments.store'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'attachment_category_id' => $category->id,
            'title' => 'Receipt upload',
            'attachable_module' => 'finance',
            'attachable_type' => 'cost_entry',
            'attachable_id' => 1,
            'attachable_label' => 'Cost entry #1',
            'file' => UploadedFile::fake()->create('receipt.pdf', 20, 'application/pdf'),
        ])->assertRedirect();

        $attachment = FarmAttachment::where('title', 'Receipt upload')->firstOrFail();
        Storage::disk('local')->assertExists($attachment->storage_path);
        $this->assertSame('receipt.pdf', $attachment->original_filename);
        $this->assertSame('pdf', $attachment->file_extension);
    }

    public function test_invalid_file_type_is_rejected(): void
    {
        Storage::fake('local');
        $user = $this->adminUser();
        [$organization, $farm] = $this->scope();

        $this->actingAs($user)->post(route('documents.attachments.store'), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'title' => 'Bad script',
            'file' => UploadedFile::fake()->create('bad.php', 1, 'text/x-php'),
        ])->assertSessionHasErrors('file');
    }

    public function test_download_route_returns_file_for_authorized_user(): void
    {
        Storage::fake('local');
        $user = $this->adminUser();
        $attachment = $this->attachment();

        $this->actingAs($user)->get(route('documents.attachments.download', $attachment))->assertOk();
    }

    public function test_attachment_metadata_can_be_updated_and_deleted(): void
    {
        $user = $this->adminUser();
        $attachment = $this->attachment();
        [$organization, $farm] = $this->scope();

        $this->actingAs($user)->put(route('documents.attachments.update', $attachment), [
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'title' => 'Updated title',
            'description' => 'Updated description',
        ])->assertRedirect();

        $this->assertSame('Updated title', $attachment->fresh()->title);

        $this->actingAs($user)->delete(route('documents.attachments.destroy', $attachment))->assertRedirect();
        $this->assertSoftDeleted('farm_attachments', ['id' => $attachment->id]);
    }

    public function test_category_crud_and_system_delete_block(): void
    {
        $user = $this->adminUser();
        [$organization] = $this->scope();

        $this->actingAs($user)->post(route('documents.categories.store'), [
            'organization_id' => $organization->id,
            'name' => 'Custom Category',
            'slug' => 'custom-category',
            'is_active' => 1,
        ])->assertRedirect();

        $category = AttachmentCategory::where('slug', 'custom-category')->firstOrFail();
        $this->actingAs($user)->put(route('documents.categories.update', $category), [
            'organization_id' => $organization->id,
            'name' => 'Custom Category Updated',
            'slug' => 'custom-category-updated',
            'is_active' => 1,
        ])->assertRedirect();
        $this->assertSame('Custom Category Updated', $category->fresh()->name);
        $this->actingAs($user)->delete(route('documents.categories.destroy', $category))->assertRedirect();

        $system = AttachmentCategory::where('is_system', true)->firstOrFail();
        $this->actingAs($user)->delete(route('documents.categories.destroy', $system))->assertStatus(422);
    }

    public function test_reports_pages_and_routes_load(): void
    {
        $user = $this->adminUser();
        foreach (['documents.reports.summary', 'documents.reports.by-category', 'documents.reports.by-source'] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }

        Artisan::call('route:list', ['--path' => 'admin/documents']);
        $this->assertTrue(Route::has('documents.attachments.download'));
        $this->assertTrue(Route::has('documents.categories.index'));
    }

    private function adminUser(): User
    {
        $this->seed(DatabaseSeeder::class);
        return User::where('email', 'admin@smartshamba.test')->firstOrFail();
    }

    private function scope(): array
    {
        return [Organization::firstOrFail(), Farm::firstOrFail()];
    }

    private function attachment(): FarmAttachment
    {
        [$organization, $farm] = $this->scope();
        Storage::disk('local')->put('attachments/test/document.txt', 'hello');

        return FarmAttachment::create([
            'organization_id' => $organization->id,
            'farm_id' => $farm->id,
            'attachment_category_id' => AttachmentCategory::first()?->id,
            'title' => 'Test attachment',
            'original_filename' => 'document.txt',
            'stored_filename' => 'document.txt',
            'storage_disk' => 'local',
            'storage_path' => 'attachments/test/document.txt',
            'mime_type' => 'text/plain',
            'file_extension' => 'txt',
            'file_size_bytes' => 5,
            'visibility' => 'private',
            'status' => 'active',
        ]);
    }

    private function memberWithRole(string $roleKey): User
    {
        $organization = Organization::firstOrFail();
        $farm = Farm::where('organization_id', $organization->id)->first();
        $role = Role::where('key', $roleKey)->firstOrFail();
        $user = User::create(['name' => 'Documents Member', 'email' => $roleKey.'-documents@smartshamba.test', 'password' => 'password123', 'status' => 'active']);
        OrganizationMembership::create(['organization_id' => $organization->id, 'farm_id' => $farm?->id, 'user_id' => $user->id, 'role_id' => $role->id, 'role_key' => $role->key, 'status' => 'active', 'joined_at' => now()]);

        return $user;
    }
}
