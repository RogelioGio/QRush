<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KdsOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->id,
            'table_id' => $this->table_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'order_items' => $this->orderItems->map(function ($item) {
                return [
                    'menu_item_id' => $item->menu_item_id,
                    'name' => $item->menuItem->name,
                    'quantity' => $item->quantity,
                ];
            }),
        ];
    }
}
