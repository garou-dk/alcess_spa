<?php

namespace Tests\Feature;

use App\Enums\OrderStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Barangay;
use App\Models\IslandGroup;
use App\Models\Municity;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\Province;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Barangay $barangay;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::factory()->create(['role_name' => RoleEnum::ADMIN->value]);
        Role::factory()->create(['role_name' => RoleEnum::CUSTOMER->value]);

        // Create admin user
        $this->adminUser = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::ADMIN->value)->first()->role_id,
        ]);

        // Create location hierarchy for address
        $islandGroup = IslandGroup::factory()->create(['island_group_name' => 'Luzon']);
        $region = Region::factory()->create([
            'island_group_id' => $islandGroup->island_group_id,
            'region_name' => 'NCR',
        ]);
        $province = Province::factory()->create([
            'region_id' => $region->region_id,
            'province_name' => 'Metro Manila',
        ]);
        $municity = Municity::factory()->create([
            'province_id' => $province->province_id,
            'municity_name' => 'Manila',
        ]);
        $this->barangay = Barangay::factory()->create([
            'municity_id' => $municity->municity_id,
            'barangay_name' => 'Ermita',
        ]);
    }

    /** @test */
    public function it_returns_delivery_report_data_with_correct_structure()
    {
        // Create a customer
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
            'full_name' => 'John Doe',
        ]);

        // Create a product
        $product = Product::factory()->create([
            'product_name' => 'Test Product',
            'product_price' => 100.00,
        ]);

        // Create an order with delivery status
        $order = Order::factory()->create([
            'user_id' => $customer->user_id,
            'barangay_id' => $this->barangay->barangay_id,
            'other_details' => '123 Test Street',
            'contact_number' => '09123456789',
            'status' => OrderStatusEnum::FOR_DELIVERY->value,
            'order_type' => 'Delivery',
            'delivery_courier' => 'LBC',
            'tracking_number' => 'TRACK123',
            'estimated_delivery_date_start' => now()->addDays(1)->format('Y-m-d'),
            'estimated_delivery_date_end' => now()->addDays(3)->format('Y-m-d'),
            'total_amount' => 150.00,
            'created_at' => now(),
        ]);

        // Create product order
        ProductOrder::factory()->create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
            'price' => 100.00,
        ]);

        // Make API request
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/delivery-report?' . http_build_query([
                'start_date' => now()->subDay()->format('Y-m-d'),
                'end_date' => now()->addDay()->format('Y-m-d'),
            ]));

        // Assert response structure
        $response->assertStatus(200);
        
        // Debug: Check actual response structure
        $responseData = $response->json();
        $this->assertArrayHasKey('status', $responseData);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertArrayHasKey('data', $responseData);
        
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                '*' => [
                    'order_id',
                    'order_public_id',
                    'customer_name',
                    'contact_number',
                    'address',
                    'delivery_courier',
                    'tracking_number',
                    'estimated_delivery_start',
                    'estimated_delivery_end',
                    'customer_received_date',
                    'total_amount',
                ]
            ]
        ]);

        // Assert data values
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data[0]['customer_name']);
        $this->assertEquals('TRACK123', $data[0]['tracking_number']);
        $this->assertEquals(150.00, $data[0]['total_amount']);
        $this->assertStringContainsString('123 Test Street', $data[0]['address']);
        $this->assertStringContainsString('Ermita', $data[0]['address']);
    }

    /** @test */
    public function it_filters_delivery_report_by_date_range()
    {
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
        ]);

        $product = Product::factory()->create();

        // Create order within date range
        $orderInRange = Order::factory()->create([
            'user_id' => $customer->user_id,
            'barangay_id' => $this->barangay->barangay_id,
            'status' => OrderStatusEnum::FOR_DELIVERY->value,
            'order_type' => 'Delivery',
            'created_at' => now()->subDays(2),
        ]);

        ProductOrder::factory()->create([
            'order_id' => $orderInRange->order_id,
            'product_id' => $product->product_id,
        ]);

        // Create order outside date range
        $orderOutOfRange = Order::factory()->create([
            'user_id' => $customer->user_id,
            'barangay_id' => $this->barangay->barangay_id,
            'status' => OrderStatusEnum::FOR_DELIVERY->value,
            'order_type' => 'Delivery',
            'created_at' => now()->subDays(10),
        ]);

        ProductOrder::factory()->create([
            'order_id' => $orderOutOfRange->order_id,
            'product_id' => $product->product_id,
        ]);

        // Request with date range
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/delivery-report?' . http_build_query([
                'start_date' => now()->subDays(3)->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should only return the order within range
        $this->assertCount(1, $data);
        $this->assertEquals($orderInRange->order_id, $data[0]['order_id']);
    }

    /** @test */
    public function it_includes_user_and_barangay_relationships()
    {
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
            'full_name' => 'Jane Smith',
        ]);

        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'user_id' => $customer->user_id,
            'barangay_id' => $this->barangay->barangay_id,
            'other_details' => '456 Main Road',
            'status' => OrderStatusEnum::SHIPPED->value,
            'order_type' => 'Delivery',
            'created_at' => now(),
        ]);

        ProductOrder::factory()->create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/delivery-report?' . http_build_query([
                'start_date' => now()->subDay()->format('Y-m-d'),
                'end_date' => now()->addDay()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');

        // Verify user relationship is loaded
        $this->assertEquals('Jane Smith', $data[0]['customer_name']);
        
        // Verify barangay relationship is loaded and address is formatted
        $this->assertStringContainsString('456 Main Road', $data[0]['address']);
        $this->assertStringContainsString('Ermita', $data[0]['address']);
        $this->assertStringContainsString('Manila', $data[0]['address']);
        $this->assertStringContainsString('Metro Manila', $data[0]['address']);
        $this->assertStringContainsString('Luzon', $data[0]['address']);
    }

    /** @test */
    public function it_only_returns_delivery_orders_with_correct_status()
    {
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
        ]);

        $product = Product::factory()->create();

        // Create orders with different statuses
        $deliveryOrder = Order::factory()->create([
            'user_id' => $customer->user_id,
            'barangay_id' => $this->barangay->barangay_id,
            'status' => OrderStatusEnum::FOR_DELIVERY->value,
            'order_type' => 'Delivery',
            'created_at' => now(),
        ]);

        $pendingOrder = Order::factory()->create([
            'user_id' => $customer->user_id,
            'barangay_id' => $this->barangay->barangay_id,
            'status' => 'Pending',
            'order_type' => 'Delivery',
            'created_at' => now(),
        ]);

        ProductOrder::factory()->create([
            'order_id' => $deliveryOrder->order_id,
            'product_id' => $product->product_id,
        ]);

        ProductOrder::factory()->create([
            'order_id' => $pendingOrder->order_id,
            'product_id' => $product->product_id,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/delivery-report?' . http_build_query([
                'start_date' => now()->subDay()->format('Y-m-d'),
                'end_date' => now()->addDay()->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');

        // Should only return orders with delivery statuses
        $this->assertCount(1, $data);
        $this->assertEquals($deliveryOrder->order_id, $data[0]['order_id']);
    }

    /** @test */
    public function it_validates_date_range_parameters()
    {
        // Test with invalid date format
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/delivery-report?' . http_build_query([
                'start_date' => 'invalid-date',
                'end_date' => now()->format('Y-m-d'),
            ]));

        $response->assertStatus(422);

        // Test with end_date before start_date
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/delivery-report?' . http_build_query([
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->subDay()->format('Y-m-d'),
            ]));

        $response->assertStatus(422);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/admin/dashboard/delivery-report?' . http_build_query([
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(401);
    }
}
