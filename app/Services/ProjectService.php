<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectService
{
    // Cache Key
    protected string $cachceKey = 'active_projects_list';

    public function showAllProject(): array
    {
        return Cache::remember($this->cachceKey, now()->addDay(), function () {
            return Project::where('is_active', true)->orderBy('created_at', 'desc')->get()->toArray();
        });
    }

    public function createProject(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $codeProject = 'PRJ-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));

            $project = Project::create([
                'code_project' => $codeProject,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'is_active' => true,
            ]);

            Cache::forget($this->cachceKey);

            return $project;
        });
    }
}
