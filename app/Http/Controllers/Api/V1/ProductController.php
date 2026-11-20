<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ProductFilter;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends APIController
{
    /**
     * GET /api/v1/products
     * Returns a paginated list (15 per page). Each product is transformed by ProductResource.
     */
    public function index(ProductFilter $filters)
    {
        // Apply the filters from the URL (?status=A, ?product=*x*, ?include=author, etc.)
        return ProductResource::collection(
            Product::filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if ($this->include('author')) {
            return new ProductResource($product->load('author'));
        }

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}