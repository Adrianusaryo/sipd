<?php

use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\ApprovalLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DocumentController as UserDocumentController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/approval-logs', [ApprovalLogController::class, 'index']);

    Route::prefix('user/documents')->middleware('role:user')->group(function () {
        Route::get('/', [UserDocumentController::class, 'index']);
        Route::get('/summary', [UserDocumentController::class, 'summary']);
        Route::post('/', [UserDocumentController::class, 'store']);
        Route::put('/{id}', [UserDocumentController::class, 'update']);
    });

    Route::prefix('admin/documents')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminDocumentController::class, 'index']);
        Route::get('/summary', [AdminDocumentController::class, 'summary']);
        Route::put('/{id}', [AdminDocumentController::class, 'update']);
        // Route::get('/export', [DocumentController::class, 'export']);
    });
});
