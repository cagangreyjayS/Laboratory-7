<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "created" event.
     */
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = $transaction->inventoryProduct; // Fixed: Changed from ->product to ->inventoryProduct

            if (!$product) {
                throw ValidationException::withMessages([
                    'inventory_product_id' => 'The selected product does not exist.',
                ]);
            }

            if ($transaction->type === 'stock_in') {
                $product->quantity += $transaction->quantity;
            } elseif ($transaction->type === 'stock_out') {
                if ($product->quantity < $transaction->quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Insufficient stock available for this transaction.',
                    ]);
                }
                $product->quantity -= $transaction->quantity;
            } elseif ($transaction->type === 'adjustment') {
                $product->quantity += $transaction->quantity;
                if ($product->quantity < 0) {
                    $product->quantity = 0;
                }
            }

            $product->save();

            // Check if product is now at or below reorder level
            if ($product->quantity <= $product->reorder_level) {
                // Fetch admin or authorized users to notify
                $users = User::all(); // Or filter by roles if roles exist
                Notification::send($users, new LowStockNotification($product));
            }
        });
    }
}