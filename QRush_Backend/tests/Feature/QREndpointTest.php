<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\Tables;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QREndpointTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    #[Test]
    public function test_valid_open_session(): void
    {
        //Preconditions
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
            'qr_token' => 'valid_token_123',
        ]);

        $closedTable = Tables::create([
            'table_number' => 2,
            'is_active' => true,
            'qr_token' => 'valid_token_789',
        ]);

        $tableSession = $table->tableSessions()->create([
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $tableSession_closed = $closedTable->tableSessions()->create([
            'status' => 'closed',
            'opened_at' => now(),
            'closed_at' => now(),
        ]);

        //Action
        $response1 = $this->getJson('/api/v1/qr/table_sessions/valid_token_123');
        $reponse2 = $this->getJson('/api/v1/qr/table_sessions/invalid_token_456');
        $reponse3 = $this->getJson('/api/v1/qr/table_sessions/valid_token_789');


        //Assertions
        $response1->assertStatus(200);
        $reponse2->assertStatus(404);
        $reponse3->assertStatus(404);
    }


    public function test_invalid_token(): void
    {
        //Preconditions
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
            'qr_token' => 'valid_token_123',
        ]);

        $tableSession = $table->tableSessions()->create([
            'status' => 'open',
            'opened_at' => now(),
        ]);


        //Action
        $reponse2 = $this->getJson('/api/v1/qr/table_sessions/invalid_token_456');


        //Assertions
        $reponse2->assertStatus(404);
    }

    public function test_closed_session(): void
    {
        //Preconditions
        $closedTable = Tables::create([
            'table_number' => 2,
            'is_active' => true,
            'qr_token' => 'valid_token_789',
        ]);

        $tableSession_closed = $closedTable->tableSessions()->create([
            'status' => 'closed',
            'opened_at' => now(),
            'closed_at' => now(),
        ]);

        //Action
        $reponse3 = $this->getJson('/api/v1/qr/table_sessions/valid_token_789');


        //Assertions
        $reponse3->assertStatus(404);
    }

    public function test_active_menu_categories_only(){
        //preconditions
        $activeCategory = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $inactiveCategory = MenuCategory::create([
            'name' => 'Desserts',
            'is_active' => false,
        ]);
        //action
        $response = $this->getJson('/api/v1/qr/menu');

        //assertions
        $response->assertStatus(200);
        $response->assertJsonMissing([
            'category' => 'Desserts',
        ]);
        $response->assertJsonFragment([
            'category' => 'Beverages',
        ]);
    }

    public function test_active_menu_items_only(){
        //precondition]
        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $activeItem = $category->items()->create([
            'name' => 'Coffee',
            'price' => 3.50,
            'is_available' => true,
        ]);
        $inactiveItem = $category->items()->create([
            'name' => 'Tea',
            'price' => 2.50,
            'is_available' => false,
        ]);

        //action
        $response = $this->getJson('/api/v1/qr/menu');

        //assertion
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Coffee',
        ]);
        $response->assertJsonMissing([
            'name' => 'Tea',
        ]);
    }

    public function test_menu_api_structure(){
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $item = $category->items()->create([
            'name' => 'Coffee',
            'price' => 3.50,
            'is_available' => true,
        ]);

        //action
        $response = $this->getJson('/api/v1/qr/menu');

        //assertion
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                [
                    'id',
                    'category',
                    'menu_items' => [
                        [
                            'id',
                            'name',
                            'price',
                            'is_available',
                        ]
                    ],
                ]
            ],
        ]);
    }

    public function test_empty_menu_items(){
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $category2 = MenuCategory::create([
            'name' => 'Desserts',
            'is_active' => true,
        ]);


        //action
        $response = $this->getJson('/api/v1/qr/menu');

        //assertion
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'category' => 'Beverages',
        ]);
        $response->assertJsonFragment([
            'category' => 'Desserts',
        ]);
        $response->assertJsonStructure([
            'message',
            'data' => [
                [
                    'id',
                    'category',
                    'menu_items' => [],
                ]
            ],
        ]);
    }

    public function test_successfully_creates_order(){
        //Preconditions
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
            'qr_token' => 'valid_token_123',
        ]);

        $tableSession = $table->tableSessions()->create([
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $item = $category->items()->create([
            'name' => 'Coffee',
            'price' => 3.50,
            'is_available' => true,
        ]);

        //Action
        $response = $this->postJson('/api/v1/qr/create_order/valid_token_123', [
            'status' => 'pending',
            'order_items' => [
                [
                    'menu_item_id' => $item->id,
                    'quantity' => 2,
                ]
            ],
        ]);


        //Assertions
        $response->assertStatus(201);
    }

    public function test_order_failed_due_to_closed_session(){
        //Preconditions
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
            'qr_token' => 'valid_token_123',
        ]);

        $tableSession = $table->tableSessions()->create([
            'status' => 'closed',
            'opened_at' => now(),
            'closed_at' => now(),
        ]);

        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $item = $category->items()->create([
            'name' => 'Coffee',
            'price' => 3.50,
            'is_available' => true,
        ]);

        //Action
        $response = $this->postJson('/api/v1/qr/create_order/valid_token_123', [
            'status' => 'pending',
            'order_items' => [
                [
                    'menu_item_id' => $item->id,
                    'quantity' => 2,
                ]
            ],
        ]);

        //Assertions
        $response->assertStatus(404);
    }

    public function test_order_failed_due_to_unavailable_item(){
        //Preconditions
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
            'qr_token' => 'valid_token_123',
        ]);

        $tableSession = $table->tableSessions()->create([
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $item = $category->items()->create([
            'name' => 'Coffee',
            'price' => 3.50,
            'is_available' => false,
        ]);

        //Action
        $response = $this->postJson('/api/v1/qr/create_order/valid_token_123', [
            'status' => 'pending',
            'order_items' => [
                [
                    'menu_item_id' => $item->id,
                    'quantity' => 2,
                ]
            ],
        ]);

        //Assertions
        $response->assertStatus(400);
    }

    public function test_order_failed_due_to_wrong_quantity(){
        //Preconditions
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
            'qr_token' => 'valid_token_123',
        ]);

        $tableSession = $table->tableSessions()->create([
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $item = $category->items()->create([
            'name' => 'Coffee',
            'price' => 3.50,
            'is_available' => true,
        ]);

        //Action
        $response = $this->postJson('/api/v1/qr/create_order/valid_token_123', [
            'status' => 'pending',
            'order_items' => [
                [
                    'menu_item_id' => $item->id,
                    'quantity' => 0,
                ]
            ],
        ]);

        //Assertions
        $response->assertStatus(422);
    }

    public function test_proper_order_response_structure(){
        //Preconditions
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
            'qr_token' => 'valid_token_123',
        ]);

        $tableSession = $table->tableSessions()->create([
            'status' => 'open',
            'opened_at' => now(),
        ]);

        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        $item = $category->items()->create([
            'name' => 'Coffee',
            'price' => 3.50,
            'is_available' => true,
        ]);

        //Action
        $response = $this->postJson('/api/v1/qr/create_order/valid_token_123', [
            'status' => 'pending',
            'order_items' => [
                [
                    'menu_item_id' => $item->id,
                    'quantity' => 2,
                ]
            ],
        ]);

        //Assertions
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'order' => [
                'table_id',
                'status',
                'table_session_id',
                'created_at',
                'updated_at',
                'id',
                'order_items' => [
                    [
                        'id',
                        'order_id',
                        'menu_item_id',
                        'quantity',
                        'price_snapshot',
                        'created_at',
                        'updated_at',
                    ]
                ],
            ],
        ]);
    }
}
