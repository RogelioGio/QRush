<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MenuCategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_menu_category_successfully(){

        //precondition

        //action
        $reponse = $this->postJson('api/v1/menu-categories', [
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        //expectation
        $reponse->assertStatus(201);
        $this->assertDatabaseHas('menu_categories', [
            'name' => 'Beverages',
            'is_active' => true,
        ]);
    }

     #[Test]
    public function reject_duplicate_menu_category_names(){
        //precondition
        MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        //action
        $reponse = $this->postJson('api/v1/menu-categories', [
            'name' => 'beverages',
            'is_active' => true,
        ]);
        //expectation
        $reponse->assertJsonValidationErrors('name');
        $reponse->assertJsonFragment([
            'The category name has already been taken.'
        ]);
        $reponse->assertStatus(422);
    }

     #[Test]
    public function reject_menu_category_creation_without_name(){
        //precondition

        //action
        $reponse = $this->postJson('api/v1/menu-categories', [
            'name' => '',
            'is_active' => true,
        ]);
        //expectation
        $reponse->assertJsonValidationErrors('name');
        $reponse->assertJsonFragment([
            'The category name is required.'
        ]);
        $reponse->assertStatus(422);
    }

     #[Test]
    public function retrieve_active_menu_categories_only(){
        //precondition
        MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        MenuCategory::create([
            'name' => 'Desserts',
            'is_active' => false,
        ]);
        //action
        $reponse = $this->getJson('api/v1/menu-categories/available');

        //expectation
        $reponse->assertJsonCount(1);
        $reponse->assertJsonFragment([
            'name' => 'Beverages',
        ]);
        $reponse->assertStatus(200);
    }

     #[Test]
    public function deactive_menu_category_without_deleting_data(){
        //precondition
        $category = MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);
        //action
        $reponse = $this->patchJson("api/v1/menu-categories/{$category->id}", [
            'name' => 'Beverages',
            'is_active' => false,
        ]);
        //expectation
        $this->assertDatabaseHas('menu_categories', [
            'id' => $category->id,
            'name' => 'Beverages',
            'is_active' => false,
        ]);
        $reponse->assertStatus(200);
    }

}
