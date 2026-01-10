<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Product;
use App\Models\Rate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerReviewReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

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
    }

    /** @test */
    public function it_returns_customer_review_report_data_with_correct_structure()
    {
        // Create a customer
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
            'full_name' => 'John Doe',
        ]);

        // Create a product
        $product = Product::factory()->create([
            'product_name' => 'Test Product',
        ]);

        // Create a review
        Rate::factory()->create([
            'user_id' => $customer->user_id,
            'product_id' => $product->product_id,
            'rate' => 5,
            'comment' => 'Great product!',
            'created_at' => now(),
        ]);

        // Make API request
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/customer-review-report');

        // Assert response structure
        $response->assertStatus(200);
        
        $responseData = $response->json();
        $this->assertArrayHasKey('data', $responseData);
        
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'review_date',
                    'customer_name',
                    'product_name',
                    'rating',
                    'comment',
                ]
            ]
        ]);

        // Assert data values
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data[0]['customer_name']);
        $this->assertEquals('Test Product', $data[0]['product_name']);
        $this->assertEquals(5, $data[0]['rating']);
        $this->assertEquals('Great product!', $data[0]['comment']);
    }

    /** @test */
    public function it_orders_reviews_by_date_descending()
    {
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
        ]);

        $product = Product::factory()->create();

        // Create reviews with different dates
        $oldReview = Rate::factory()->create([
            'user_id' => $customer->user_id,
            'product_id' => $product->product_id,
            'comment' => 'Old review',
            'created_at' => now()->subDays(5),
        ]);

        $newReview = Rate::factory()->create([
            'user_id' => $customer->user_id,
            'product_id' => $product->product_id,
            'comment' => 'New review',
            'created_at' => now(),
        ]);

        $middleReview = Rate::factory()->create([
            'user_id' => $customer->user_id,
            'product_id' => $product->product_id,
            'comment' => 'Middle review',
            'created_at' => now()->subDays(2),
        ]);

        // Request reviews
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/customer-review-report');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should return 3 reviews in descending order (newest first)
        $this->assertCount(3, $data);
        $this->assertEquals('New review', $data[0]['comment']);
        $this->assertEquals('Middle review', $data[1]['comment']);
        $this->assertEquals('Old review', $data[2]['comment']);
    }

    /** @test */
    public function it_includes_user_and_product_relationships()
    {
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
            'full_name' => 'Jane Smith',
        ]);

        $product = Product::factory()->create([
            'product_name' => 'Amazing Widget',
        ]);

        Rate::factory()->create([
            'user_id' => $customer->user_id,
            'product_id' => $product->product_id,
            'rate' => 4,
            'comment' => 'Good quality',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/customer-review-report');

        $response->assertStatus(200);
        $data = $response->json('data');

        // Verify user relationship is loaded
        $this->assertEquals('Jane Smith', $data[0]['customer_name']);
        
        // Verify product relationship is loaded
        $this->assertEquals('Amazing Widget', $data[0]['product_name']);
    }

    /** @test */
    public function it_handles_empty_comments()
    {
        $customer = User::factory()->create([
            'role_id' => Role::where('role_name', RoleEnum::CUSTOMER->value)->first()->role_id,
        ]);

        $product = Product::factory()->create();

        // Create review without comment
        Rate::factory()->create([
            'user_id' => $customer->user_id,
            'product_id' => $product->product_id,
            'rate' => 3,
            'comment' => null,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/admin/dashboard/customer-review-report');

        $response->assertStatus(200);
        $data = $response->json('data');

        // Should return empty string for null comment
        $this->assertCount(1, $data);
        $this->assertEquals('', $data[0]['comment']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/admin/dashboard/customer-review-report');

        $response->assertStatus(401);
    }
}
