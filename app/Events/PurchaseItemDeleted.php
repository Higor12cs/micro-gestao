<?php

namespace App\Events;

use App\Models\PurchaseItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseItemDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public PurchaseItem $purchaseItem) {}
}
