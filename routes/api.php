<?php

use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\LastUpdatedController;
use App\Http\Middleware\VerifyExportToken;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::middleware(['throttle:5,1', VerifyExportToken::class])->group(function () {
    Route::get('/export', ExportController::class)->name('api.export');
    Route::get('/last-updated', LastUpdatedController::class)->name('api.last-updated');

    // Keeps unknown paths behind the token, so that unauthenticated callers cannot discover which routes exist.
    Route::any('{path}', fn () => abort(
        Response::HTTP_NOT_FOUND,
        Response::$statusTexts[Response::HTTP_NOT_FOUND]
    ))->where('path', '.*');
});
