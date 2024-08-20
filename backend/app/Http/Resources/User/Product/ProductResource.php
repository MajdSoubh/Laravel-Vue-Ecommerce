<?php

namespace App\Http\Resources\User\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "slug" => $this->slug,
            "price" => $this->price,
            "quantity" => $this->quantity,
            "description" => $this->description,
            "published" => $this->published,
            "images" => $this->images,
            "categories" => $this->categories,


        ];
    }
}
