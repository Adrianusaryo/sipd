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
        Route::get('/summary', [UserDocumentController::class, 'summary']);
        Route::get('/', [UserDocumentController::class, 'findAllDocuments']);
        Route::get('/{document:number_registration}', [UserDocumentController::class, 'findDocument']);
        Route::post('/', [UserDocumentController::class, 'store']);
        Route::put('/{document:number_registration}', [UserDocumentController::class, 'update']);
    });

    Route::prefix('admin/documents')->middleware('role:admin')->group(function () {
        Route::get('/summary', [AdminDocumentController::class, 'summary']);
        Route::get('/', [AdminDocumentController::class, 'findAllDocuments']);
        Route::get('/{document:number_registration}', [AdminDocumentController::class, 'findDocument']);
        Route::put('/{document:number_registration}', [AdminDocumentController::class, 'update']);
    });
});
