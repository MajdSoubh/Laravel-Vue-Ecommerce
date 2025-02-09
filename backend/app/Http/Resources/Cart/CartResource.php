<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\Product\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class CartResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'product' => $this->whenLoaded('product', function ()
            {
                return [
                    "id" => $this->product->id,
                    "title" => $this->product->title,
                    "slug" => $this->product->slug,
                    "price" => $this->product->price,
                    "description" => $this->product->description,
                    'created_at' => $this->product->created_at,
                    'updated_at' => $this->product->updated_at,
                    "images" => $this->product->images,
                ];
            }),
        ];
    }
}
