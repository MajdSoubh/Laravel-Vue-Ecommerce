<?php

namespace App\Http\Resources\Order;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $date = Carbon::parse($this->created_at);
        return [
            "id" => $this->id,
            "status" => $this->status,
            "total_price" => $this->total_price,
            "client" => $this->whenLoaded('client'),
            "details" => $this->whenLoaded('details'),
            "items" => $this->whenLoaded('items'),
            "items_count" => $this->items_count,
            "created_by" => $this->created_by,
            "updated_by" => $this->updated_by,
            "created_at" => $date->toDayDateTimeString(),
            "updated_at" => $this->updated_at
        ];
    }
}
