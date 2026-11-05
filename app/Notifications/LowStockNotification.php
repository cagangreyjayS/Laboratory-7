<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // Stores in database for the bell icon and sends email
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Low Stock Alert: ' . $this->product->name)
                    ->line('The product "' . $this->product->name . '" has reached a low stock level.')
                    ->line('Current Quantity: ' . $this->product->quantity)
                    ->line('Reorder Level: ' . $this->product->reorder_level)
                    ->action('View Inventory', route('products.index'))
                    ->line('Please reorder materials as soon as possible.');
    }

    /**
     * Get the array representation of the notification (stored in database).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => $this->product->quantity,
            'reorder_level' => $this->product->reorder_level,
            'message' => 'Product ' . $this->product->name . ' is running low on stock (' . $this->product->quantity . ' left).'
        ];
    }
}