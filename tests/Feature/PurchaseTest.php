<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Outlet;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $staff;
    protected $owner;
    protected $supplier;
    protected $ingredient;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed users
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
        ]);

        $this->owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'role' => 'owner',
        ]);

        // Seed supplier
        $this->supplier = Supplier::create([
            'name' => 'PT Kopi Indonesia',
            'phone' => '0812345678',
            'email' => 'info@kopi.com',
            'address' => 'Jakarta, Indonesia',
        ]);

        // Seed ingredient
        $this->ingredient = Ingredient::create([
            'name' => 'Espresso Beans',
            'unit' => 'gram',
            'stock' => 100.00,
            'minimum_stock' => 10.00,
        ]);

        // Seed default outlet and attach ingredient
        $defaultOutlet = Outlet::create([
            'name' => 'Gudang Utama',
            'is_default' => true,
        ]);

        $defaultOutlet->ingredients()->attach($this->ingredient->id, [
            'stock' => 100.00,
            'minimum_stock' => 10.00,
        ]);
    }

    // --- SUPPLIER WEB TESTS ---

    public function test_web_supplier_index_requires_auth()
    {
        $response = $this->get('/suppliers');
        $response->assertRedirect('/login');
    }

    public function test_web_supplier_index_accessible_to_all_authenticated_users()
    {
        $response = $this->actingAs($this->admin)->get('/suppliers');
        $response->assertStatus(200);

        $response = $this->actingAs($this->staff)->get('/suppliers');
        $response->assertStatus(200);

        $response = $this->actingAs($this->owner)->get('/suppliers');
        $response->assertStatus(200);
    }

    public function test_web_supplier_store_restricted_by_role()
    {
        // Owner forbidden (403)
        $response = $this->actingAs($this->owner)->post('/suppliers', [
            'name' => 'Supplier Baru',
        ]);
        $response->assertStatus(403);

        // Staff allowed
        $response = $this->actingAs($this->staff)->post('/suppliers', [
            'name' => 'PT Gula Manis',
            'phone' => '0822112233',
        ]);
        $response->assertRedirect('/suppliers');
        $this->assertDatabaseHas('suppliers', ['name' => 'PT Gula Manis']);
    }

    // --- PURCHASE ORDER WEB TESTS ---

    public function test_web_po_create_accessible_to_admin_and_staff()
    {
        $response = $this->actingAs($this->staff)->get('/purchase-orders/create');
        $response->assertStatus(200);

        $response = $this->actingAs($this->owner)->get('/purchase-orders/create');
        $response->assertStatus(403);
    }

    public function test_web_po_store_and_receiving_flow()
    {
        // 1. Store PO (status: pending)
        $response = $this->actingAs($this->staff)->post('/purchase-orders', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'ingredient_id' => $this->ingredient->id,
                    'quantity' => 500.00,
                    'unit_price' => 200.00,
                ]
            ]
        ]);

        $response->assertRedirect('/purchase-orders');
        
        $po = PurchaseOrder::first();
        $this->assertNotNull($po);
        $this->assertEquals('pending', $po->status);
        $this->assertEquals(100000.00, $po->total_amount); // 500 * 200 = 100000
        $this->assertEquals(100.00, $this->ingredient->fresh()->stock); // Stock not yet updated

        // 2. Receive PO (status: received, increments stock)
        $response = $this->actingAs($this->admin)->post("/purchase-orders/{$po->id}/receive");
        $response->assertRedirect("/purchase-orders/{$po->id}");

        $this->assertEquals('received', $po->fresh()->status);
        $this->assertEquals(600.00, $this->ingredient->fresh()->stock); // 100 + 500 = 600

        // Check stock movement log
        $this->assertDatabaseHas('stock_movements', [
            'ingredient_id' => $this->ingredient->id,
            'type' => 'in',
            'quantity' => 500.00,
            'description' => 'Penerimaan PO: ' . $po->po_number,
        ]);
    }

    // --- API TESTS ---

    public function test_api_suppliers_crud()
    {
        // List suppliers
        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/suppliers');
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'PT Kopi Indonesia']);

        // Create supplier
        $response = $this->actingAs($this->staff, 'sanctum')->postJson('/api/suppliers', [
            'name' => 'PT Sirup Premium',
            'phone' => '021998877',
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('suppliers', ['name' => 'PT Sirup Premium']);
    }

    public function test_api_purchase_orders()
    {
        // Create PO via API
        $response = $this->actingAs($this->staff, 'sanctum')->postJson('/api/purchase-orders', [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'ingredient_id' => $this->ingredient->id,
                    'quantity' => 100,
                    'unit_price' => 150,
                ]
            ]
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'purchase_order']);

        $poId = $response->json('purchase_order.id');

        // Receive PO via API
        $response = $this->actingAs($this->admin, 'sanctum')->postJson("/api/purchase-orders/{$poId}/receive");
        $response->assertStatus(200);

        $this->assertEquals(200.00, $this->ingredient->fresh()->stock); // 100 + 100 = 200
    }
}
