<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document\DocumentRequest;
use App\Http\Requests\Document\DocumentUpdateByApplicant;
use App\Http\Requests\Document\DocumentUpdateByVerificator;
use App\Http\Response\ApiResponse;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $document_service) {}

    // public function show()
    // {
    //     $result = $this->applicant_service->showAllRequest();

    //     return ApiResponse::success($result, 'success show all documents list', 200);
    // }

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
