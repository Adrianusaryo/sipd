<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

#[Fillable(['code_project', 'title', 'description', 'is_active'])]
class Project extends Model
{
    #[Override]
    protected function casts()
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function applicantDocuments(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class, 'project_id');
    }
}
