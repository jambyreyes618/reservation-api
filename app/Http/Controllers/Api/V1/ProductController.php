<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Filters\V1\ProductFilter;
use App\Http\Requests\Api\V1\ReplaceProductRequest;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Requests\Api\V1\UpdateProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends ApiController
{
    /**
     * GET /api/v1/products
     * Lists products with optional filtering and pagination.
     */
    public function index(ProductFilter $filters)
    {
        return ProductResource::collection(
            Product::filter($filters)->paginate()
        );
    }

    /**
     * GET /api/v1/products/{product}
     * Returns a single product.
     */
    public function show($product_id)
    {
        try {
            $product = Product::findOrFail($product_id);

            return new ProductResource($product);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Product cannot be found.', 404);
        }
    }

    /**
     * POST /api/v1/products
     * Creates a product, but only if the policy allows this user to do so.
     */
    public function store(StoreProductRequest $request)
    {
        // Ask ProductPolicy::store() (checks the ability stored in the token)
        if ($this->isAble('store', Product::class)) {
            return new ProductResource(
                Product::create($request->mappedAttributes())
            );
        }

        return $this->notAuthorized('You are not authorized to create that resource');
    }

    /**
     * PATCH /api/v1/products/{product}
     * Updates a product if the policy allows it.
     */
    public function update(UpdateProductRequest $request, $product_id)
    {
        try {
            $product = Product::findOrFail($product_id);
            $this->authorize('update', $product);
            $product->update($request->mappedAttributes());

            return new ProductResource($product);
        } catch (ModelNotFoundException $exception) {
            return $this->error('Product cannot be found.', 404);
        } catch (AuthorizationException $exception) {
            return $this->notAuthorized('You are not authorized to update that resource');
        }
    }

    /**
     * PUT /api/v1/products/{product}
     * Replaces the whole product, but only if the policy allows it.
     */
    public function replace(ReplaceProductRequest $request, Product $product)
    {
        if ($this->isAble('replace', $product)) {
            $product->update($request->mappedAttributes());

            return new ProductResource($product);
        }

        return $this->notAuthorized('You are not authorized to replace that resource');
    }

    /**
     * DELETE /api/v1/products/{product}
     * Deletes the product, but only if the policy allows it.
     */
    public function destroy(Product $product)
    {
        if ($this->isAble('delete', $product)) {
            $product->delete();

            return $this->ok('Product successfully deleted');
        }

        return $this->notAuthorized('You are not authorized to delete that resource');
    }
}