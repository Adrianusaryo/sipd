<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectService
{
    // Cache Key
    protected string $cacheKey = 'active_projects_list';

    private function clearProjectCache(): void
    {
        DB::table('cache')->where('key', 'like', config('cache.prefix').$this->cacheKey.'%')->delete();
    }

    public function showProject(int $perPage = 10, int $page = 1): array
    {
        $dynamicKey = "{$this->cacheKey}_page_{$page}_per_{$perPage}";

        return Cache::remember($dynamicKey, now()->addHours(1), function () use ($perPage) {
            $paginator = Project::where('is_active', true)->orderBy('created_at', 'asc')->paginate($perPage);

            return [
                'items' => array_map(fn ($item) => $item->toArray(), $paginator->items()),
                'total' => $paginator->total(),
                'perPage' => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
            ];
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
