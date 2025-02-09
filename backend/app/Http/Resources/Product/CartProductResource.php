<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CartProductResource extends JsonResource
{
    public function toArray(Request $request)
    {

        return [
            "id" => $this->id,
            "title" => $this->title,
            "slug" => $this->slug,
            "price" => $this->price,
            "description" => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "images" => $this->images,
        ];
    }
}
