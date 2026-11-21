<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorsController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserProductsController;
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

// Products of one user: GET /users/{user}/products
Route::apiResource('users.products', UserProductsController::class)
    ->only(['index', 'store', 'destroy'])
    ->scoped(); // the product must belong to the user in the URL (otherwise 404)

// PUT and PATCH use our own method names, so they are registered by hand
Route::put('users/{user}/products/{product}', [UserProductsController::class, 'replace'])
    ->scopeBindings();
Route::patch('users/{user}/products/{product}', [UserProductsController::class, 'update'])
    ->scopeBindings();