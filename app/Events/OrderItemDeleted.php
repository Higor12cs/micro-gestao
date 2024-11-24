<?php

namespace App\Events;

use App\Models\OrderItem;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderItemDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public OrderItem $orderItem) {}
}
