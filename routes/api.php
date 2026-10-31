<?php

use App\Http\Controllers\Api\ExportController;
use App\Http\Middleware\VerifyExportToken;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:5,1', VerifyExportToken::class])->group(function () {
    Route::get('/export', ExportController::class)->name('api.export');
});
