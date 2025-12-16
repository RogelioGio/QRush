<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MenuCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function prevent_duplicate_menu_category_names(): void
    {

        //PreCondition
        MenuCategory::create([
            'name' => 'Beverages',
            'is_active' => true,
        ]);

        //Action
        $response = $this->postJson('/api/v1/menu-categories', [
            'name' => 'Beverages',
            'is_active' => true,
        ]);

        //Expected Result
        $response->assertStatus(422)
                    ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function case_insensitve_duplicate_name_check(): void
    {
        dump(config('database.default'));
        //Precondition
        MenuCategory::create([
            'name' => 'Desserts',
            'is_active' => true,
        ]);
        //Action
        $reponse = $this->postJson('/api/v1/menu-categories', [
            'name' => 'desserts',
            'is_active' => true,
        ]);
        //Expected Result
        $reponse->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    //** @test */
    public function prevent_editing_inactive_category(): void
    {
        //Precondition

        //Action

        //Expected Result

    }

}
