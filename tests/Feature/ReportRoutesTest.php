<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::factory()->create(['role_id' => 1, 'role_name' => RoleEnum::ADMIN->value]);
        Role::factory()->create(['role_id' => 2, 'role_name' => RoleEnum::STAFF->value]);
        Role::factory()->create(['role_id' => 3, 'role_name' => RoleEnum::CUSTOMER->value]);
    }

    public function test_online_sales_report_route_requires_authentication()
    {
        $response = $this->getJson('/api/admin/reports/online-sales');
        
        $response->assertStatus(401);
    }

    public function test_walkin_sales_report_route_requires_authentication()
    {
        $response = $this->getJson('/api/admin/reports/walkin-sales');
        
        $response->assertStatus(401);
    }

    public function test_customer_reviews_report_route_requires_authentication()
    {
        $response = $this->getJson('/api/admin/reports/customer-reviews');
        
        $response->assertStatus(401);
    }

    public function test_online_sales_report_route_requires_admin_or_staff_role()
    {
        $customer = User::factory()->create(['role_id' => 3]);
        
        $response = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/admin/reports/online-sales');
        
        $response->assertStatus(403);
    }

    public function test_walkin_sales_report_route_requires_admin_or_staff_role()
    {
        $customer = User::factory()->create(['role_id' => 3]);
        
        $response = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/admin/reports/walkin-sales');
        
        $response->assertStatus(403);
    }

    public function test_customer_reviews_report_route_requires_admin_or_staff_role()
    {
        $customer = User::factory()->create(['role_id' => 3]);
        
        $response = $this->actingAs($customer, 'sanctum')
            ->getJson('/api/admin/reports/customer-reviews');
        
        $response->assertStatus(403);
    }

    public function test_admin_can_access_online_sales_report()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/reports/online-sales');
        
        $response->assertStatus(200);
    }

    public function test_staff_can_access_walkin_sales_report()
    {
        $staff = User::factory()->create(['role_id' => 2]);
        
        $response = $this->actingAs($staff, 'sanctum')
            ->getJson('/api/admin/reports/walkin-sales');
        
        $response->assertStatus(200);
    }

    public function test_admin_can_access_customer_reviews_report()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/reports/customer-reviews');
        
        $response->assertStatus(200);
    }
}
