<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Stock;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $items = $order->orderItems;

        foreach ($items as $item) {

            $stock = Stock::find($item->stock_id);
            $stock_update = $stock->stock_quantity_virtual - $item->quantity;

            $stock->update([
                "stock_quantity_virtual" => $stock_update
            ]);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
