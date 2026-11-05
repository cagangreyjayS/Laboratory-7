<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReorderService;

class CheckReorderPoints extends Command
{
    protected $signature = 'inventory:check-reorder';
    protected $description = 'Check inventory stock levels against reorder points and auto-generate purchase orders.';

    public function handle(ReorderService $reorderService)
    {
        $this->info('Checking inventory reorder thresholds...');

        $orders = $reorderService->generatePurchaseOrders();

        $count = count($orders);
        if ($count > 0) {
            $this->info("Successfully generated {$count} automatic purchase order(s) for low stock items.");
        } else {
            $this->info('All stock levels are optimal. No purchase orders generated.');
        }

        return Command::SUCCESS;
    }
}