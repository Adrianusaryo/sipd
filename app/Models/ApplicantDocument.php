<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable(['number_registration', 'project_id', 'applicant_id', 'verificator_id', 'title', 'description', 'status', 'verificator_notes', 'submitted_at', 'approved_at'])]
class ApplicantDocument extends Model
{
    #[Override]
    protected function casts()
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function verificator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verificator_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DocumentFile::class, 'applicant_id');
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class, 'applicant_id')->lastest();
    }
}
