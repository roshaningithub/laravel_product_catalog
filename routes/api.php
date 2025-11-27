<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Product\Controllers\ProductController;

Route::post('/product', [ProductController::class, 'create']);
Route::put('/product', [ProductController::class, 'update']);
Route::delete('/product', [ProductController::class, 'delete']);
