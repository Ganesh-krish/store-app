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
    public function test_multiple_products_in_single_order(): void
    {
        Queue::fake();

        $product1 = Product::factory()->create(['price' => 100.00, 'tax_percentage' => 18, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 50.00, 'tax_percentage' => 12, 'stock' => 5]);

        $response = $this->postJson('/api/orders', [
            'customer' => ['name' => 'Test User', 'email' => 'test@example.com'],
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.subtotal', '250.00');
        $response->assertJsonPath('data.tax', '42.00');
        $response->assertJsonPath('data.grand_total', '292.00');

        $this->assertDatabaseHas('products', ['id' => $product1->id, 'stock' => 8]);
        $this->assertDatabaseHas('products', ['id' => $product2->id, 'stock' => 4]);
    }

    public function test_invalid_quantity_zero(): void
    {
        Queue::fake();

        $product = Product::factory()->create(['price' => 100.00, 'tax_percentage' => 18, 'stock' => 10]);

        $response = $this->postJson('/api/orders', [
            'customer' => ['name' => 'Test User', 'email' => 'test@example.com'],
            'items' => [
                ['product_id' => $product->id, 'quantity' => 0],
            ],
        ]);

        $response->assertUnprocessable();
        $this->assertDatabaseMissing('orders', []);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }

    public function test_existing_customer_reuse(): void
    {
        Queue::fake();

        $customer = Customer::factory()->create(['name' => 'Original Name', 'email' => 'existing@example.com']);
        $product = Product::factory()->create(['price' => 100.00, 'tax_percentage' => 18, 'stock' => 10]);

        $response = $this->postJson('/api/orders', [
            'customer' => ['name' => 'Different Name', 'email' => 'existing@example.com'],
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertCreated();
        $this->assertEquals(1, Customer::where('email', 'existing@example.com')->count());
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'Original Name']);
    }
}
