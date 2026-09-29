<?php

namespace App\Http\Controllers\Admin;

use App\Exports\VerificatorDocumentExport;
use App\Http\Controllers\Controller;
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
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $document_service) {}

    // Index by Admin
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
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->document_service->showDocumentByAdmin($limit, $page);

        return ApiResponse::pagination($result, 'success show all document', 200);
    }

    // Update By Admin
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
    public function update(DocumentUpdateByVerificator $request, Document $document): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();

        $result = $this->document_service->updateByAdmin($document, $data, $user);

        return ApiResponse::success($result, 'success update document status', 200);
    }

    public function export(Request $request): BinaryFileResponse
    {
        // Tangkap filter query param jika Verifikator mau filter berdasarkan status/tanggal
        $filters = $request->only(['status', 'start_date', 'end_date']);

        $fileName = 'rekap_dokumen_verifikasi_'.now()->format('Ymd_His').'.xlsx';

        return Excel::download(new VerificatorDocumentExport($filters), $fileName);
    }
}
