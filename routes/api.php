<?php

use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\ApprovalLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DocumentController;
use App\Http\Controllers\User\DocumentController as UserDocumentController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/approval-logs', [ApprovalLogController::class, 'hello']);

    Route::prefix('documents')->group(function () {
        Route::get('/', [UserDocumentController::class, 'index']);
        Route::post('/', [UserDocumentController::class, 'store']);
        Route::put('/{document}', [UserDocumentController::class, 'update']);
    })->middleware('role:user');

    Route::prefix('documents')->group(function () {
        Route::get('/', [AdminDocumentController::class, 'index']);
        Route::put('/{document}', [DocumentController::class, 'update']);
        Route::get('/export', [DocumentController::class, 'export']);
    })->middleware('role:admin');
});
