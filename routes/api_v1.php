<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorsController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserProductsController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('products', ProductController::class)->except(['update']);
    Route::put('products/{product}', [ProductController::class, 'replace']);
    Route::patch('products/{product}', [ProductController::class, 'update']);

    Route::apiResource('users', UserController::class)->except(['update']);
    Route::put('users/{user}', [UserController::class, 'replace']);
    Route::patch('users/{user}', [UserController::class, 'update']);

    Route::apiResource('authors', AuthorsController::class)->except(['store', 'update', 'delete']);

    Route::apiResource('authors.products', UserProductsController::class)->except(['update']);
    Route::put('authors/{author}/products/{product}', [UserProductsController::class, 'replace']);
    Route::patch('authors/{author}/products/{product}', [UserProductsController::class, 'update']);
});