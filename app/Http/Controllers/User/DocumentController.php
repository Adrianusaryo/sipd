<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\Admin\DocumentUpdateRequest;
use App\Http\Requests\Document\User\DocumentRequest;
use App\Http\Resources\Document\User\DocumentResource;
use App\Http\Resources\Document\User\DocumentStoreResource;
use App\Http\Resources\Document\User\DocumentSummaryResource;
use App\Http\Resources\Document\User\DocumentUpdateResource;
use App\Http\Response\ApiResponse;
use App\Models\Document;
use App\Services\User\DocumentService;
use App\Swagger\ApiErrorDoc;
use App\Swagger\ApiPaginationDoc;
use App\Swagger\JsonSchemaRef;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $document_service) {}

    #[OA\Get(
        path: '/user/documents/summary',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        responses: [
            new ApiPaginationDoc(200, 'success get all documents', new JsonSchemaRef('UserDocumentSummaryResource')),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function summary(Request $request): JsonResponse
    {
        $result = $this->document_service->showSummary($request->user()->id);

        return ApiResponse::success(new DocumentSummaryResource($result), 'success show document summary', 200);
    }

    #[OA\Get(
        path: '/user/documents',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new ApiPaginationDoc(200, 'success get documents', new JsonSchemaRef('UserDocumentResource')),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->document_service->showDocument($request->user()->id, $limit, $page);

        $result['items'] = DocumentResource::collection($result['items']);

        return ApiResponse::pagination($result, 'success show all document', 200);
    }

    // Store
    #[OA\Post(
        path: 'user/documents',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonSchemaRef('UserDocumentUpdateRequest','multipart/form-data')
        ),
        responses: [
            new ApiPaginationDoc(200, 'success get all project', new JsonSchemaRef('UserDocumentStoreResource')),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]

    public function store(DocumentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $files = $request->file('files');

        $user = $request->user();

        $result = $this->document_service->createDocument($data, $files, $user);

        return ApiResponse::success(new DocumentStoreResource($result), 'success create document', 201);
    }

    // Update
    #[OA\Put(
        path: '/user/documents/{id}',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 3)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonSchemaRef('UserDocumentUpdateRequest','multipart/form-data')
        ),
        responses: [
            new ApiPaginationDoc(200, 'success get all project', new JsonSchemaRef('UserDocumentUpdateResource')),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]

    public function update(DocumentUpdateRequest $request, Document $id): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();

        $files = $request->file('files');

        $result = $this->document_service->updateDocument($id, $data, $files, $user);

        return ApiResponse::success(new DocumentUpdateResource($result), 'success update document', 200);
    }
}
