<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['applicant_id', 'actor_id', 'action', 'status_from', 'status_to', 'notes'])]
class ApprovalLog extends Model
{
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'applicant_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
