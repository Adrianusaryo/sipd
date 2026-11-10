<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\Admin\DocumentUpdateRequest;
use App\Http\Resources\Document\Admin\DocumentDetailResource;
use App\Http\Resources\Document\Admin\DocumentResource;
use App\Http\Resources\Document\Admin\DocumentSummaryResource;
use App\Http\Resources\Document\Admin\DocumentUpdateResource;
use App\Http\Response\ApiResponse;
use App\Models\Document;
use App\Services\Admin\DocumentService;
use App\Swagger\ApiErrorDoc;
use App\Swagger\ApiPaginationDoc;
use App\Swagger\ApiSuccessDoc;
use App\Swagger\JsonSchemaRef;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $document_service) {}

    // Summary Documents
    #[OA\Get(
        path: '/admin/documents/summary',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        responses: [
            new ApiPaginationDoc(200, 'success get all documents', new JsonSchemaRef('AdminDocumentSummaryResource')),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function summary(): JsonResponse
    {
        $result = $this->document_service->showSummary();

        return ApiResponse::success(new DocumentSummaryResource($result), 'success show document summary', 200);
    }

    // Find All Documents
    #[OA\Get(
        path: '/admin/documents',
        tags: ['Admin'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new ApiPaginationDoc(200, 'success get all documents', new JsonSchemaRef('AdminDocumentResource')),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function findAllDocuments(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->document_service->findAllDocument($limit, $page);

        $result['items'] = DocumentResource::collection($result['items']);

        return ApiResponse::pagination($result, 'success show all document', 200);
    }

    #[OA\Get(
        path: '/admin/documents/{id}',
        tags: ['Admin'],
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
            new ApiPaginationDoc(200, 'success get document', new JsonSchemaRef('AdminDocumentDetailResource')),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function findDocument(Document $id): JsonResponse
    {
        $result = $this->document_service->findDocument($id);

        return ApiResponse::success(new DocumentDetailResource($result), 'success find document',200);
    }

    // Update Document
    #[OA\Put(
        path: '/admin/documents/{id}',
        tags: ['Admin'],
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
            content: new JsonSchemaRef('AdminDocumentUpdateRequest')
        ),
        responses: [
            new ApiSuccessDoc(201, 'success update status', new JsonSchemaRef('AdminDocumentUpdateResource')),
            new ApiErrorDoc(422, 'unprocessable entity'),
        ]
    )]
    public function update(DocumentUpdateRequest $request, Document $id): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();

        $result = $this->document_service->updateDocument($id, $data, $user);

        return ApiResponse::success(new DocumentUpdateResource($result), 'success update document status', 200);
    }

    // public function export(Request $request): BinaryFileResponse
    // {
    //     // Tangkap filter query param jika Verifikator mau filter berdasarkan status/tanggal
    //     $filters = $request->only(['status', 'start_date', 'end_date']);

    //     $fileName = 'rekap_dokumen_verifikasi_'.now()->format('Ymd_His').'.xlsx';

    //     return Excel::download(new VerificatorDocumentExport($filters), $fileName);
    // }
}
