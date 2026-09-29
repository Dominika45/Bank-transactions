<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TransactionController;

Route::post('/imports', [ImportController::class, 'store']);
Route::get('/imports', [ImportController::class, 'index']);
Route::get('/imports/{id}', [ImportController::class, 'show']);
Route::get('/transactions', [TransactionController::class, 'index']);