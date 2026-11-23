<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Users
 *
 * Manage users.
 */
class UserController extends APIController
{
    public function index(AuthorFilter $filters)
    {
        return UserResource::collection(
            User::filter($filters)->paginate()
        );
    }

    public function show(User $user)
    {
        if ($this->include('products')) {
            return new UserResource($user->load('products'));
        }

        return new UserResource($user);
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, User $user)
    {
        //
    }

    public function destroy(User $user)
    {
        //
    }
}