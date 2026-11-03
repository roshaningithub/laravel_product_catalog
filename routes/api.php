<?php

use App\Modules\Product\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/product', [ProductController::class, 'create']);
Route::put('/product', [ProductController::class, 'update']);
Route::delete('/product', [ProductController::class, 'delete']);
