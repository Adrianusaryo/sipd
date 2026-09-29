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
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        $logs = $this->logService->getLogsByUser($request->user(), $page, $limit);

        return ApiResponse::pagination($logs, 'success get all logs');
    }
}
