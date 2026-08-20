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

    private function clearProjectCache(): void
    {
        Cache::forget($this->cachceKey);
    }

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

            $this->clearProjectCache();

            return $project;
        });
    }

    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'title' => $data['title'] ?? $project->title,
                'description' => $data['description'] ?? $project->description,
                'is_active' => $data['is_active'] ?? $project->is_active,
            ]);

            $this->clearProjectCache();

            return $project;
        });
    }

    public function removeProject(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            $result = $project->delete();

            $this->clearProjectCache();

            return $result;
        });
    }
}
