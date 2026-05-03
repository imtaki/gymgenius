<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id'          => 'user_' . $this->id,
            'type'        => 'user',
            'attributes' => [
                'name'        => $this->name,
                'email'       => $this->email,
                'is_verified' => (bool) $this->is_verified,
                'role'        => $this->role,
            ],
        ];
    }
}
