<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorsController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserProductsController;
use Illuminate\Support\Facades\Route;

// Public routes: no token needed
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes: require a valid JWT token
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Products: full CRUD (index, show, store, update, destroy)
    Route::apiResource('products', ProductController::class);

    // Authors are read only: list and show
    Route::apiResource('authors', AuthorsController::class)->only(['index', 'show']);

    // Products of one user: GET /users/{user}/products
    Route::apiResource('users.products', UserProductsController::class)->only(['index']);
});