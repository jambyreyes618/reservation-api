<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\V1\UserResource;
use App\Models\User;

/**
 * @group Authors
 *
 * Read product authors.
 */
class AuthorsController extends APIController
{
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::select('users.*')
                ->join('products', 'users.id', '=', 'products.user_id')
                ->filter($filters)
                ->distinct()
                ->paginate()
        );
    }

    public function show(User $author)
    {
        if ($this->include('products')) {
            return new UserResource($author->load('products'));
        }

        return new UserResource($author);
    }
}