<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        $this->order->loadMissing([
            'customer',
            'orderItems.product',
        ]);

        Log::info('Order confirmation email simulated.', [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->customer->name,
            'customer_email' => $this->order->customer->email,
            'grand_total' => $this->order->grand_total,
        ]);
    }
}
