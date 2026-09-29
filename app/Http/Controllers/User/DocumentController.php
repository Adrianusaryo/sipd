<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Document\DocumentRequest;
use App\Http\Requests\Document\DocumentUpdateByApplicant;
use App\Http\Response\ApiResponse;
use App\Models\Document;
use App\Services\DocumentService;
use App\Swagger\ApiErrorDoc;
use App\Swagger\ApiPaginationDoc;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $document_service) {}

    #[OA\Get(
        path: '/documents/applicant',
        tags: ['Applicant'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new ApiPaginationDoc(200, 'success get all project', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'code_project', type: 'string', example: 'PRJ-20260827-WL0R'),
                new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->document_service->showDocumentByUser($request->user()->id, $limit, $page);

        return ApiResponse::pagination($result, 'success show all document', 200);
    }

    // Store
    #[OA\Post(
        path: '/documents',
        tags: ['Applicant'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/DocumentRequest')
            )
        ),
        responses: [
            new ApiPaginationDoc(200, 'success get all project', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function store(DocumentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $files = $request->file('files');

        $user = $request->user();

        $result = $this->document_service->createRequest($data, $files, $user);

        return ApiResponse::success($result, 'success create document', 201);
    }

    // Updated by User
    #[OA\Put(
        path: '/documents/{document}/applicant',
        tags: ['Applicant'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'document',
                in: 'path',
                required: true,
                description: 'ID Dokumen yang sedang direvisi',
                schema: new OA\Schema(type: 'integer', example: 3)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/DocumentRequest')
            )
        ),
        responses: [
            new ApiPaginationDoc(200, 'success get all project', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'code_project', type: 'string', example: 'PRJ-20260827-WL0R'),
                new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function update(DocumentUpdateByApplicant $request, Document $document): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();

        $files = $request->file('files');

        $result = $this->document_service->updateByUser($document, $data, $files, $user);

        return ApiResponse::success($result, 'success update document', 200);
    }
}
