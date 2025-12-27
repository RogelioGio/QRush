<?php

namespace Tests\Feature;


use App\Models\MenuCategory;
use App\Models\Tables;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;


class BackendFlowTest extends TestCase
{
    use RefreshDatabase;
    #[Test]

    public function complete_transaction_flow_successfully(){
        //precondition
        $menucategory = MenuCategory::create([
            'name' => 'Items',
            'is_active' => true,
        ]);
        $item_1 = $menucategory->Items()->create([
            'name' => 'Item 1',
            'description' => 'Description for Item 1',
            'price' => 10.00,
            'is_active' => true,
        ]);
        $item_2 = $menucategory->Items()->create([
            'name' => 'Item 2',
            'description' => 'Description for Item 2',
            'price' => 15.00,
            'is_active' => true,
        ]);
        $table = Tables::create([
            'table_number' => 1,
            'is_active' => true,
        ]);

        //action
        $action_1 = $this->postJson("/api/v1/table_sessions/{$table->id}/open");
        $action_2 = $this->postJson("/api/v1/orders", [
            'table_id' => $table->id,
            'status' => "pending",
            'order_items' => [
                ['menu_item_id' => $item_1->id, 'quantity' => 2],
                ['menu_item_id' => $item_2->id, 'quantity' => 1],
            ],
        ]);

        $action_3 = $this->patchJson("/api/v1/orders/{$action_2->json('order.id')}/status", [
            'status' => 'confirmed',
        ]);
        $action_4 = $this->patchJson("/api/v1/kds/orders/{$action_2->json('order.id')}/status", [
            'status' => 'preparing',
        ]);
        $action_5 = $this->patchJson("/api/v1/kds/orders/{$action_2->json('order.id')}/status", [
            'status' => 'ready',
        ]);
        $action_6 = $this->patchJson("/api/v1/orders/{$action_2->json('order.id')}/status", [
            'status' => 'served',
        ]);
        $action_8 = $this->getJson("/api/v1/billing/{$action_2->json('order.table_session_id')}/preview");
        $action_9 = $this->postJson("/api/v1/billing/{$action_2->json('order.table_session_id')}/payment", [
            'payment_method' => 'credit_card',
            'reference_no' => 'REF123456',
        ]);
        $action_10 = $this->postJson("/api/v1/billing/{$action_9->json('payment_id')}/payment/confirm");

        //expectation
        $action_1->assertStatus(200);
        $action_2->assertStatus(201);
        $action_3->assertStatus(200);
        $action_4->assertStatus(200);
        $action_5->assertStatus(200);
        $action_6->assertStatus(200);
        $action_8->assertStatus(200);
        $action_9->assertStatus(200);
        $action_10->assertStatus(200);
    }
}
