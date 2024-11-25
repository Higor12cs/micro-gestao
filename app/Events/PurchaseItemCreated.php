<?php

namespace App\Events;

use App\Models\PurchaseItem;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PurchaseItemCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public PurchaseItem $purchaseItem) {}
}
