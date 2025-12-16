<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MenuItemTest extends TestCase
{
     #[Test]
    public function create_menu_item_under_valid_category(): void{
        //precondition
        $existingCategory = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        //action
        $reponse = $this->postJson('api/v1/menu-items', [
            "name" => "Coke",
            "price" => 35,
            "menu_category_id" => $existingCategory->id,
            "is_available" => true
        ]);

        //expectation
        $this->assertDatabaseHas('menu_items', [
            "name" => "Coke",
            "price" => 35,
            "menu_category_id" => $existingCategory->id,
            "is_available" => true
        ]);
        $reponse->assertStatus(201);
    }
    #[Test]
    public function reject_menu_item_with_invalid_category(): void{
        //precondition

        //action
        $reponse = $this->postJson('api/v1/menu-items', [
            "name" => "Invalid Item",
            "price" => 50,
            "menu_category_id" => 999,
            "is_available" => true
        ]);

        //expectation
        $reponse->assertJsonValidationErrors('menu_category_id');
        $reponse->assertStatus(422);
    }
    #[Test]
    public function retrieve_menu_item_by_id(): void{
        //precondition
        $existingCategory = MenuCategory::create([
            'name' => 'Snacks',
            'is_active' => true,
        ]);
        $menuItem = $existingCategory->items()->create([
            "name" => "Fries",
            "price" => 45,
            "is_available" => true
        ]);

        //action
        $reponse = $this->getJson('api/v1/menu-items/' . $menuItem->id);

        //expectation
        $reponse->assertJsonFragment([
            "name" => "Fries",
            "price" => 45.00,
            "menu_category_id" => $existingCategory->id,
            "is_available" => true
        ]);
        $reponse->assertStatus(200);
    }
    #[Test]
    public function persist_menu_item_availability_status(): void{
        //precondition
        $existingCategory = MenuCategory::create([
            'name' => 'Snacks',
            'is_active' => true,
        ]);
        $menuItem = $existingCategory->items()->create([
            "name" => "Nachos",
            "price" => 55,
            "is_available" => true
        ]);

        //action
        $reponse = $this->putJson('api/v1/menu-items/' . $menuItem->id, [
            "name" => "Nachos",
            "menu_category_id" => $existingCategory->id,
            "price" => 55,
            "is_available" => false
        ]);

        //expectation
        $this->assertDatabaseHas('menu_items', [
            "id" => $menuItem->id,
            "is_available" => false
        ]);
        $reponse->assertStatus(200);
    }
}
