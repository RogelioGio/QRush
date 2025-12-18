<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderItemsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function store_multiple_order_items_correctly(): void
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
                        "menu_item_id" => $fries->id, "quantity" => 3
                    ]
                ]
            ]
        );

        //assertions
        $this->assertDatabaseHas('order_items', [
            "menu_item_id" => $burger->id,
            "quantity" => 2,
        ]);
        $this->assertDatabaseHas('order_items', [
            "menu_item_id" => $fries->id,
            "quantity" => 3,
        ]);
        $reposnse->assertStatus(201);
    }

    #[Test]
    public function respect_quantity_for_each_order_item(): void
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
                        "menu_item_id" => $burger->id, "quantity" => 5,
                    ],
                    [
                        "menu_item_id" => $fries->id, "quantity" => 1
                    ]
                ]
            ]
        );

        //assertions
        $this->assertDatabaseHas('order_items', [
            "menu_item_id" => $burger->id,
            "quantity" => 5,
        ]);
        $this->assertDatabaseHas('order_items', [
            "menu_item_id" => $fries->id,
            "quantity" => 1,
        ]);
        $reposnse->assertStatus(201);
    }

    #[Test]
    public function store_price_snapshot_server_side(): void
    {
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Fast Food',
            'is_active' => true,
        ]);
        $burger = MenuItem::create([
            "name" => "Burger",
            "price" => 120,
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
                    ]
                ]
            ]
        );

        //assertions
        $this->assertDatabaseHas('order_items', [
            "menu_item_id" => $burger->id,
            "price_snapshot" => 120,
        ]);
        $reposnse->assertStatus(201);
    }

    #[Test]
    public function preserve_historical_pricing_after_menu_price_change(): void
    {
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Fast Food',
            'is_active' => true,
        ]);
        $burger = MenuItem::create([
            "name" => "Burger",
            "price" => 150,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);

        //action
        $this->postJson('api/v1/orders', [
                "table_id" => 1,
                "status" => "pending",
                "order_items" => [
                    [
                        "menu_item_id" => $burger->id, "quantity" => 1,
                    ]
                ]
            ]
        );

        //change menu item price
        $burger->price = 200;
        $burger->save();

        //assertions
        $this->assertDatabaseHas('order_items', [
            "menu_item_id" => $burger->id,
            "price_snapshot" => 150,
        ]);
    }

    #[Test]
    public function ignore_or_reject_client_sent_price(): void
    {
        //preconditions
        $category = MenuCategory::create([
            'name' => 'Fast Food',
            'is_active' => true,
        ]);
        $burger = MenuItem::create([
            "name" => "Burger",
            "price" => 180,
            "menu_category_id" => $category->id,
            "is_available" => true
        ]);

        //action
        $this->postJson('api/v1/orders', [
                "table_id" => 1,
                "status" => "pending",
                "order_items" => [
                    [
                        "menu_item_id" => $burger->id,
                        "quantity" => 1,
                        "price_snapshot" => 50 //malicious client price attempt
                    ]
                ]
            ]
        );

        //assertions
        $this->assertDatabaseHas('order_items', [
            "menu_item_id" => $burger->id,
            "price_snapshot" => 180,
        ]);
    }
}
