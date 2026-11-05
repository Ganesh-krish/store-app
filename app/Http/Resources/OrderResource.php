<?php

namespace App\Http\Resources;

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
         return [
            'id' => $this->id,

            'customer' => [
                'name' => $this->customer->name,
                'email' => $this->customer->email,
            ],

            'items' => OrderItemResource::collection(
                $this->whenLoaded('orderItems')
            ),

            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'grand_total' => $this->grand_total,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
