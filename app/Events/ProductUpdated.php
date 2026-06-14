<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;
    public string $action; // 'created' | 'updated' | 'deleted'

    public function __construct(Product $product, string $action)
    {
        $this->product = $product;
        $this->action  = $action;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('pos'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'product.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action'  => $this->action,
            'product' => [
                'id'        => $this->product->id,
                'name'      => $this->product->name,
                'stock'     => $this->product->stock,
                'price'     => $this->product->price,
                'category'  => $this->product->category,
                'is_active' => $this->product->is_active,
            ],
        ];
    }
}