<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'emailVerifiedAt' => $this->email_verified_at,
                'createdAt' => $this->created_at,
                'updatedAt' => $this->updated_at,
            ],
            // The products array, present only when requested with ?include=products
            'includes' => ProductResource::collection($this->whenLoaded('products')),
            // Link to this user
            'links' => [
                'self' => route('authors.show', ['author' => $this->id]),
            ],
        ];
    }
}