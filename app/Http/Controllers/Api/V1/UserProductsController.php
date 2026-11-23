<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\ProductFilter;
use App\Http\Requests\Api\V1\StoreProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use App\Models\User;

/**
 * @group User Products
 *
 * Manage products belonging to a specific user.
 */
class UserProductsController extends APIController
{
    public function index(User $user, ProductFilter $filters)
    {
        return ProductResource::collection(
            Product::where('user_id', $user->id)->filter($filters)->paginate()
        );
    }

    public function store(StoreProductRequest $request, User $user)
    {
        if ($this->isAble('store', Product::class)) {
            $attributes = $request->mappedAttributes();
            $attributes['user_id'] = $user->id;

            return new ProductResource(Product::create($attributes));
        }

        return $this->notAuthorized('You are not authorized to create that resource');
    }
}