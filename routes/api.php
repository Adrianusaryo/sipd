<?php

use App\Http\Controllers\ApprovalLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Authenticated & Logout
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('role:applicant|verificator')->group(function () {
        Route::prefix('approval-logs')->group(function () {
            Route::get('/', [ApprovalLogController::class, 'index']);
        });
    });

    Route::middleware('role:applicant')->group(function () {
        // Master Project
        Route::prefix('projects')->group(function () {
            Route::get('/', [ProjectController::class, 'index']);
            Route::post('/', [ProjectController::class, 'store']);
            Route::put('/{project}', [ProjectController::class, 'update']);
            Route::delete('/{project}', [ProjectController::class, 'remove']);
        });

        Route::prefix('documents')->group(function () {
            Route::get('/applicant', [DocumentController::class, 'index_applicant']);
            Route::post('/', [DocumentController::class, 'store']);
            Route::put('/{document}/applicant', [DocumentController::class, 'updateByApplicant']);
        });
    });

    Route::middleware('role:verificator')->group(function () {
        Route::prefix('documents')->group(function () {
            Route::get('/verificator', [DocumentController::class, 'index_verificator']);
            Route::put('/{document}/verificator', [DocumentController::class, 'updateByVerificator']);
        });
    });
});
