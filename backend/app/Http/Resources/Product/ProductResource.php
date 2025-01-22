<?php

namespace App\Http\Resources\Product;

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
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "images" => $this->images,
            "categories" => $this->categories,
            $this->mergeWhen(($request->user() && $request->user()->is_admin), [
                "published" => $this->published,
                "created_by" => $this->created_by,
                "updated_by" => $this->updated_by,
            ]),


        ];
    }
}
