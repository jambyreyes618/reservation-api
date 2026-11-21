<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorsController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

// GET /products (list), GET /products/{product} (one product), POST /products (create), and DELETE /products/{product} (delete)
Route::apiResource('products', ProductController::class)->only(['index', 'show', 'store', 'destroy']);

// PUT replaces the whole product (our own method name: replace)
Route::put('products/{product}', [ProductController::class, 'replace']);

// PATCH updates only the fields that were sent
Route::patch('products/{product}', [ProductController::class, 'update']);

// Authors are read only: list and show
Route::apiResource('authors', AuthorsController::class)->only(['index', 'show']);