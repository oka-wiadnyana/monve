<?php

use App\Http\Controllers\BackupTableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/backup-tables', [BackupTableController::class, 'index']);

// Endpoint: GET /api/backup-tables/sipp_pn_jakarta (untuk detail satu database)
Route::get('/backup-tables/{id}', [BackupTableController::class, 'show']);
