<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmationJob;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_an_order_successfully(): void
    {
        Queue::fake();

        $customer = Customer::factory()->create();

        $product = Product::factory()->create([
            'price' => 1000.00,
            'tax_percentage' => 18,
            'stock' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertCreated();

        $response->assertJsonPath('data.subtotal', '2000.00');
        $response->assertJsonPath('data.tax', '360.00');
        $response->assertJsonPath('data.grand_total', '2360.00');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'subtotal' => 2000.00,
            'tax' => 360.00,
            'grand_total' => 2360.00,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8,
        ]);

        Queue::assertPushed(SendOrderConfirmationJob::class);
    }


    public function test_order_cannot_be_created_when_stock_is_insufficient(): void
    {
        Queue::fake();

        $customer = Customer::factory()->create();

        $product = Product::factory()->create([
            'price' => 1000.00,
            'tax_percentage' => 18,
            'stock' => 5,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                ],
            ],
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseMissing('orders', [
            'customer_id' => $customer->id,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
        ]);

        Queue::assertNothingPushed();
    }
}
