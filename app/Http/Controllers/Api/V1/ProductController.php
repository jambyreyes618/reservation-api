<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ProductFilter;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;

class ProductController extends APIController
{
    /**
     * GET /api/v1/products
     */
    public function index(ProductFilter $filters)
    {
        return ProductResource::collection(
            Product::filter($filters)->paginate()
        );
    }

    /**
     * GET /api/v1/products/{product}
     */
    public function show(Product $product)
    {
        if ($this->include('author')) {
            return new ProductResource($product->load('author'));
        }

        return new ProductResource($product);
    }

    /**
     * POST /api/v1/products
     * Validates the body with StoreProductRequest, then creates the product.
     * Laravel answers 201 Created automatically for a newly created resource.
     */
    public function store(StoreProductRequest $request)
    {
        // mappedAttributes() converts the nested JSON into database columns
        return new ProductResource(
            Product::create($request->mappedAttributes())
        );
    }
}