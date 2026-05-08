<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Documents\Models\AttachmentCategory;
use App\Modules\Documents\Models\FarmAttachment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentsAttachmentsSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Receipt', 'Invoice', 'Delivery Note', 'Photo Evidence', 'Vet Report', 'Lab Report', 'Warranty', 'Certificate', 'Contract', 'Other'];
        foreach ($categories as $index => $name) {
            AttachmentCategory::query()->updateOrCreate(
                ['organization_id' => null, 'slug' => Str::slug($name)],
                ['name' => $name, 'is_system' => true, 'is_active' => true, 'sort_order' => $index + 1],
            );
        }

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        $category = AttachmentCategory::query()->where('slug', 'other')->first();

        if (! $organization || ! $category) {
            return;
        }

        $path = 'attachments/'.$organization->id.'/'.($farm ? 'farm-'.$farm->id : 'general').'/demo/demo-document.txt';
        Storage::disk('local')->put($path, 'Demo attachment placeholder for local/testing only.');

        FarmAttachment::query()->updateOrCreate(
            ['organization_id' => $organization->id, 'storage_path' => $path],
            [
                'farm_id' => $farm?->id,
                'attachment_category_id' => $category->id,
                'title' => 'Demo attachment placeholder',
                'description' => 'Local/testing placeholder attachment.',
                'original_filename' => 'demo-document.txt',
                'stored_filename' => 'demo-document.txt',
                'storage_disk' => 'local',
                'mime_type' => 'text/plain',
                'file_extension' => 'txt',
                'file_size_bytes' => strlen('Demo attachment placeholder for local/testing only.'),
                'checksum_sha256' => hash('sha256', 'Demo attachment placeholder for local/testing only.'),
                'visibility' => 'private',
                'status' => 'active',
            ],
        );
    }
}
