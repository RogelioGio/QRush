<?php

namespace Tests\Feature;

use App\Models\Roles;
use App\Models\Tables;
use App\Models\TableSessions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Table;
use App\Models\TableSession;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $kitchenUser;
    protected $cashierUser;
    protected $managementUser;
    protected $table;
    protected $tableSession;
    protected $menuCategory;
    protected $menuItem;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->managementRole = Roles::create(['name' => 'management']);
        $this->cashierRole = Roles::create(['name' => 'cashier']);
        $this->kitchenRole = Roles::create(['name' => 'kitchen']);

        // Create users
        $this->kitchenUser = User::create([
            'role_id' => $this->kitchenRole->id,
            'first_name' => 'Kitchen',
            'last_name' => 'User',
            'access_pin' => '9012',
            'password' => Hash::make('password'),
        ]);

        $this->cashierUser = User::create([
            'role_id' => $this->cashierRole->id,
            'first_name' => 'Cashier',
            'last_name' => 'User',
            'access_pin' => '5678',
            'password' => Hash::make('password'),
        ]);

        $this->managementUser = User::create([
            'role_id' => $this->managementRole->id,
            'first_name' => 'Management',
            'last_name' => 'User',
            'access_pin' => '1234',
            'password' => Hash::make('password'),
        ]);

        // Create table
        $this->table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
        ]);

        // Create table session
        $this->tableSession = TableSessions::create([
            'table_id' => $this->table->id,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        // Create menu category
        $this->menuCategory = MenuCategory::create([
            'name' => 'Test Category',
            'is_active' => true,
        ]);

        // Create menu item
        $this->menuItem = MenuItem::create([
            'menu_category_id' => $this->menuCategory->id,
            'name' => 'Test Item',
            'price' => 100,
            'is_available' => true,
        ]);

        // Create order
        $this->order = Order::create([
            'table_id' => $this->table->id,
            'table_session_id' => $this->tableSession->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function kitchen_access_rules()
    {
        $this->actingAs($this->kitchenUser, 'sanctum');

        // Allowed endpoints
        $this->getJson('/api/v1/kds/orders')->assertStatus(200);


        // Forbidden endpoints
        $this->getJson("/api/v1/cashier/orders/summary")->assertStatus(403);
        $this->postJson("/api/v1/cashier/billing/{$this->tableSession->id}/payment", [
            'payment_method' => 'gcash',
            'reference_no' => '123'
        ])->assertStatus(403);
        $this->getJson("/api/v1/management/reports/daily")->assertStatus(403);
    }

    /** @test */
    public function cashier_access_rules()
    {
        $this->actingAs($this->cashierUser, 'sanctum');

        // Allowed endpoints
        $this->getJson("/api/v1/cashier/orders/summary")->assertStatus(200);
        $this->putJson("/api/v1/cashier/orders/{$this->order->id}/status", ['status' => 'confirmed'])->assertStatus(200);

        // Forbidden endpoints
        $this->getJson("/api/v1/kds/orders")->assertStatus(403);
        $this->getJson("/api/v1/management/reports/daily")->assertStatus(403);
    }

    /** @test */
    public function management_access_rules()
    {
        $this->actingAs($this->managementUser, 'sanctum');

        // Allowed endpoints
        $this->getJson("/api/v1/management/reports/daily")->assertStatus(200);
        $this->putJson("/api/v1/management/menu-categories/{$this->menuCategory->id}/activate", ['is_active' => true])->assertStatus(200);

        // Forbidden endpoints
        $this->getJson("/api/v1/kds/orders")->assertStatus(403);
        $this->getJson("/api/v1/cashier/orders/summary")->assertStatus(403);
    }
}
