<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;



class OrderTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function create_order_with_valid_items(): void
    {
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Fast Food',
            'is_active' => true,
        ]);
        $burger = MenuItem::create([
            "name" => "Burger",
            "price" => 100,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);
        $fries = MenuItem::create([
            "name" => "Fries",
            "price" => 50,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);

        //action
        $reposnse = $this->postJson('api/v1/orders', [
                "table_id" => 1,
                "status" => "pending",
                "order_items" => [
                    [
                        "menu_item_id" => $burger->id, "quantity" => 2,
                    ],
                    [
                        "menu_item_id" => $fries->id, "quantity" => 2
                    ]
                ]
            ]
        );

        //assertions
        dump($reposnse->json());
        $reposnse->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            "table_id" => 1,
            "status" => "pending",
        ]);
        $this->assertDatabaseHas('order_items', [
            "order_id" => $reposnse->json('order.id'),
            "menu_item_id" => $burger->id,
            "quantity" => 2,
            "price_snapshot" => 100.00,
        ]);
    }

    #[Test]
    public function reject_order_without_items(): void
    {
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Fast Food',
            'is_active' => true,
        ]);
        MenuItem::create([
            "name" => "Burger",
            "price" => 100,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);
        MenuItem::create([
            "name" => "Fries",
            "price" => 50,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);

        //action
        $reposnse = $this->postJson('api/v1/orders', [
                "table_id" => 1,
                "status" => "pending",
                "order_items" => []
            ]
        );

        //asseertions
        $reposnse->assertJsonValidationErrors('order_items');
        $reposnse->assertStatus(422);
    }

    #[Test]
    public function validate_order_response_structure(): void
    {
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Fast Food',
            'is_active' => true,
        ]);
        $burger = MenuItem::create([
            "name" => "Burger",
            "price" => 100,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);
        $fries = MenuItem::create([
            "name" => "Fries",
            "price" => 50,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);

        //action
        $reposnse = $this->postJson('api/v1/orders', [
                "table_id" => 1,
                "status" => "pending",
                "order_items" => [
                    [
                        "menu_item_id" => $burger->id, "quantity" => 2,
                    ],
                    [
                        "menu_item_id" => $fries->id, "quantity" => 2
                    ]
                ]
            ]
        );

        //asseertions
        $reposnse->assertJsonStructure([
            'order' => [
                'id',
                'table_id',
                'status',
                'created_at',
                'updated_at',
                'order_items' => [
                    '*' => [
                        'id',
                        'order_id',
                        'menu_item_id',
                        'quantity',
                        'price_snapshot',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        ]);
        $reposnse->assertStatus(201);
    }
}
