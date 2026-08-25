<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document\DocumentRequest;
use App\Http\Requests\Document\DocumentUpdateByApplicant;
use App\Http\Requests\Document\DocumentUpdateByVerificator;
use App\Http\Response\ApiResponse;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $document_service) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $result = $this->document_service->showDocumentRequest($perPage, $page);

        return ApiResponse::pagination($result, 'success show all document', 200);
    }

    public function store(DocumentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $files = $request->file('files');

        $user = $request->user();

        $result = $this->document_service->createRequest($data, $files, $user);

        return ApiResponse::success($result, 'success create document', 201);
    }

    public function updateByApplicant(DocumentUpdateByApplicant $request, Document $document): JsonResponse
    {
        $data = $request->validated();

        $files = $request->file('files');

        $result = $this->document_service->updateByApplicant($document, $data, $files);

        return ApiResponse::success($result, 'success update document', 200);
    }

    public function updateByVerificator(DocumentUpdateByVerificator $request, Document $document): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();

        $result = $this->document_service->updateByVerificator($document, $data, $user);

        return ApiResponse::success($result, 'success update document status', 200);
    }
}
