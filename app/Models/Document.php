<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable(['number_registration', 'user_id', 'admin_id',  'title', 'description', 'status', 'admin_notes', 'submitted_at', 'approved_at'])]

class Document extends Model
{
    protected $table = 'documents';

    #[Override]
    protected function casts()
    {
        return [
            'status' => DocumentStatus::class,
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DocumentFile::class, 'document_id');
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class, 'document_id')->latest();
    }
}
