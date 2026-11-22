<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\V1\Abilities;

class StoreProductRequest extends BaseProductRequest
{
    /**
     * Validation rules for creating a product (deny by default).
     */
    public function rules(): array
    {
        $userIdAttr = $this->routeIs('products.store')
            ? 'data.relationships.user.data.id'
            : 'author';

        $user = $this->user();
        $userRule = 'required|integer|exists:users,id';

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.product' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            // Default: the author must be the logged-in user
            $userIdAttr => $userRule . '|size:' . $user->id,
        ];

        // Only the broad ability lifts the restriction (managers)
        if ($user->tokenCan(Abilities::CreateProduct)) {
            $rules[$userIdAttr] = $userRule;
        }

        return $rules;
    }
}