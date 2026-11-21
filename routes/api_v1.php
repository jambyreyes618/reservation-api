<?php

use App\Http\Controllers\Api\V1\AuthorsController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

// GET /products (list), GET /products/{product} (one product), POST /products (create), and DELETE /products/{product} (delete)
Route::apiResource('products', ProductController::class)->only(['index', 'show', 'store', 'destroy']);

// Authors are read only: list and show
Route::apiResource('authors', AuthorsController::class)->only(['index', 'show']);