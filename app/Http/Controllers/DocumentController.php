<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document\DocumentRequest;
use App\Http\Requests\Document\DocumentUpdateByApplicant;
use App\Http\Requests\Document\DocumentUpdateByVerificator;
use App\Http\Response\ApiResponse;
use App\Models\Document;
use App\Services\DocumentService;
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

    // Index by applicant
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
    public function index_applicant(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->document_service->showDocumentByApplicant($request->user()->id, $perPage, $page);

        return ApiResponse::pagination($result, 'success show all document', 200);
    }

    // Index by verificator
    #[OA\Get(
        path: '/documents/verificator',
        tags: ['Verificator'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new ApiPaginationDoc(200, 'success get all documents', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'code_project', type: 'string', example: 'PRJ-20260827-WL0R'),
                new OA\Property(property: 'title', type: 'string', example: 'Kampung Nelayan Merah Putih'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),
            new ApiErrorDoc(422, 'Unprocessable Entity'),
        ]
    )]
    public function index_verificator(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->document_service->showDocumentByVerificator($perPage, $page);

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
                new OA\Property(property: 'code_project', type: 'string', example: 'PRJ-20260827-WL0R'),
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

    // Updated by applicant
    #[OA\Put(
        path: '/documents/{document}',
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
    public function updateByApplicant(DocumentUpdateByApplicant $request, Document $document): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();

        $files = $request->file('files');

        $result = $this->document_service->updateByApplicant($document, $data, $files, $user);

        return ApiResponse::success($result, 'success update document', 200);
    }

    // Update By Verificator
    #[OA\Put(
        path: '/documents/{id}/verificator',
        tags: ['Verificator'],
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
            content: new JsonSchemaRef('DocumentUpdateRequestVerificator')
        ),
        responses: [
            new ApiSuccessDoc(201, 'success update status', [
                new OA\Property(property: 'id', type: 'integer', example: '1'),
                new OA\Property(property: 'description', type: 'string', example: 'Pengajuan izin analisis dampak pembangunan program Kampung Nelayan Merah Putih.'),
            ]),

            new ApiErrorDoc(401, 'unauthenticated'),
            new ApiErrorDoc(404, 'project not found'),
            new ApiErrorDoc(422, 'unprocessable entity'),
        ]
    )]
    public function updateByVerificator(DocumentUpdateByVerificator $request, Document $document): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();

        $result = $this->document_service->updateByVerificator($document, $data, $user);

        return ApiResponse::success($result, 'success update document status', 200);
    }
}
