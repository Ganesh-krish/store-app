<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subtotal = (float) $this->unit_price * $this->quantity;

        $tax = $subtotal * ((float) $this->tax_percentage / 100);

        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'quantity' => $this->quantity,

            'unit_price' => $this->unit_price,
            'tax_percentage' => $this->tax_percentage,

            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax' => number_format($tax, 2, '.', ''),
            'total' => number_format($subtotal + $tax, 2, '.', ''),
        ];
    }
}
