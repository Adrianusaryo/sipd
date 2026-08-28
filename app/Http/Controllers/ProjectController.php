<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Http\Response\ApiResponse;
use App\Models\Project;
use App\Services\ProjectService;
use App\Swagger\ApiPaginationDoc;
use App\Swagger\ApiSuccessDoc;
use App\Swagger\JsonSchemaRef;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProjectController extends Controller
{
    public function __construct(protected ProjectService $project_service) {}

    // Show Project
    #[OA\Get(
        path: '/projects',
        tags: ['Project'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new ApiPaginationDoc(200, 'success show all project', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'code_project', type: 'string', example: 'PRJ-20260827-WL0R'),
                new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->project_service->showProject($perPage, $page);

        return ApiResponse::pagination($result, 'success show all projects', 200);
    }

    // Store Project
    #[OA\Post(
        path: '/projects',
        tags: ['Project'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonSchemaRef('ProjectRequest')
        ),
        responses: [
            new ApiSuccessDoc(201, 'create project success', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'code_project', type: 'string', example: 'PRJ-20260827-WL0R'),
                new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),
        ]
    )]
    public function store(ProjectRequest $request)
    {
        $result = $this->project_service->createProject($request->validated());

        if (! $result) {
            return ApiResponse::error(null, 'please follow the rule', 500);
        }

        return ApiResponse::success($result, 'success create project', 201);
    }

    // Update Project
    #[OA\Put(
        path: '/projects/{id}',
        tags: ['Project'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonSchemaRef('ProjectRequest')
        ),
        responses: [
            new ApiSuccessDoc(201, 'success update project', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'code_project', type: 'string', example: 'PRJ-20260827-WL0R'),
                new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),
        ]
    )]
    public function update(ProjectUpdateRequest $request, Project $project)
    {
        $result = $this->project_service->updateProject($project, $request->validated());

        return ApiResponse::success($result, 'success update project', 200);
    }

    // Remove Project
    #[OA\Delete(
        path: '/projects/{id}',
        tags: ['Project'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new ApiSuccessDoc(201, 'success remove project'),
        ]
    )]
    public function remove(Project $project)
    {
        $this->project_service->removeProject($project);

        return ApiResponse::success(null, 'success remove project', 200);
    }
}
