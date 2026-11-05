<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Jobs\SendOrderConfirmationJob;

class OrderService
{
    public function createOrder(array $data): Order
        {
            $order = DB::transaction(function () use ($data) {

                // Step 1: Find or create the customer
                $customer = Customer::firstOrCreate(
                    ['email' => $data['customer']['email']],
                    ['name' => $data['customer']['name']]
                );

                // Step 2: Get product IDs and sort them
                $productIds = collect($data['items'])
                    ->pluck('product_id')
                    ->sort()
                    ->values();

                // Step 3: Lock products to prevent concurrent stock updates
                $products = Product::whereIn('id', $productIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = 0;
                $totalTax = 0;
                $orderItems = [];

                // Step 4: Validate stock and calculate totals
                foreach ($data['items'] as $item) {

                    $product = $products->get($item['product_id']);
                    $quantity = $item['quantity'];

                    if (!$product || $product->stock < $quantity) {
                        throw ValidationException::withMessages([
                            'items' => [
                                "Insufficient stock for product ID {$item['product_id']}."
                            ]
                        ]);
                    }

                    $unitPrice = (int) round((float) $product->price * 100);
                    $lineSubtotal = $unitPrice * $quantity;

                    $lineTax = (int) round(
                        $lineSubtotal * (float) $product->tax_percentage / 100
                    );

                    $subtotal += $lineSubtotal;
                    $totalTax += $lineTax;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'tax_percentage' => $product->tax_percentage,
                    ];

                    // Step 5: Deduct stock
                    $product->decrement('stock', $quantity);
                }

                // Step 6: Create the order
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'subtotal' => number_format($subtotal / 100, 2, '.', ''),
                    'tax' => number_format($totalTax / 100, 2, '.', ''),
                    'grand_total' => number_format(
                        ($subtotal + $totalTax) / 100,
                        2,
                        '.',
                        ''
                    ),
                ]);

                // Step 7: Create order items
                $order->orderItems()->createMany($orderItems);

                return $order->load([
                    'customer',
                    'orderItems.product',
                ]);
            });

            SendOrderConfirmationJob::dispatch($order);

            return $order;
        }
}
