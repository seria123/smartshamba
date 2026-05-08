<?php

namespace App\Modules\Documents\Models;

use App\Models\User;
use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FarmAttachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'farm_id', 'attachment_category_id', 'attachable_module', 'attachable_type',
        'attachable_id', 'attachable_label', 'title', 'description', 'original_filename', 'stored_filename',
        'storage_disk', 'storage_path', 'mime_type', 'file_extension', 'file_size_bytes', 'checksum_sha256',
        'visibility', 'status', 'uploaded_by', 'deleted_by',
    ];

    public function organization(): BelongsTo { return $this->belongsTo(Organization::class); }
    public function farm(): BelongsTo { return $this->belongsTo(Farm::class); }
    public function category(): BelongsTo { return $this->belongsTo(AttachmentCategory::class, 'attachment_category_id'); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function deletedBy(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }
}
