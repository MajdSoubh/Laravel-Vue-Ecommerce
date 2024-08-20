<?php

namespace App\Http\Resources\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'label' => $this->name,
        ];

        if ($this->children)
        {

            $data['children'] = CategoryTreeResource::collection($this->children);
        }
        return $data;
    }
}
