<?php

namespace App\Http\Controllers;

use App\Http\Response\ApiResponse;
use App\Services\ApprovalLogsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApprovalLogController extends Controller
{
    public function __construct(protected ApprovalLogsService $logService) {}

    public function index(Request $request): JsonResponse
    {
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);

        $logs = $this->logService->getLogsByUser($request->user(), $page, $perPage);

        return ApiResponse::pagination($logs, 'success get all logs');
    }
}
