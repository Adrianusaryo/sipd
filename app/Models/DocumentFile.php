<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Override;

#[Fillable(['applicant_id', 'document_type', 'verificator_notes', 'file_path', 'file_name', 'file_mime', 'file_size', 'version'])]
class DocumentFile extends Model
{
    protected $table = 'documents_files';

    #[Override]
    public function casts()
    {
        return [
            'file_size' => 'integer',
            'version' => 'integer',
        ];
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'applicant_id');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
