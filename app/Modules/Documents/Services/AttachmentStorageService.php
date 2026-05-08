<?php

namespace App\Modules\Documents\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentStorageService
{
    public const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'];

    public function store(UploadedFile $file, int $organizationId, ?int $farmId): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $storedFilename = Str::uuid()->toString().'.'.$extension;
        $farmSegment = $farmId ? 'farm-'.$farmId : 'general';
        $path = 'attachments/'.$organizationId.'/'.$farmSegment.'/'.now()->format('Y/m').'/'.$storedFilename;
        $contents = file_get_contents($file->getRealPath());

        Storage::disk('local')->put($path, $contents);

        return [
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'storage_disk' => 'local',
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_extension' => $extension,
            'file_size_bytes' => $file->getSize() ?: strlen($contents),
            'checksum_sha256' => hash('sha256', $contents),
        ];
    }

    public function delete(string $disk, string $path): bool
    {
        return Storage::disk($disk)->delete($path);
    }
}
