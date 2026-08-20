<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Models\Project;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(protected ProjectService $project_service) {}

    public function show()
    {
        $result = $this->project_service->showAllProject();

        return ApiResponse::success($result, 'success show all projects list', 200);
    }

    public function store(ProjectRequest $request)
    {
        $result = $this->project_service->createProject($request->validated());

        if (! $result) {
            return ApiResponse::error(null, 'please follow the rule', 500);
        }

        return ApiResponse::success($result, 'success create project', 201);
    }

    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $result = $this->project_service->updateProject($project, $request->validated());

        return ApiResponse::success($result, 'success update project', 200);
    }

    public function remove(Project $project)
    {
        $this->project_service->removeProject($project);

        return ApiResponse::success(null, 'success remove project', 200);
    }
}
