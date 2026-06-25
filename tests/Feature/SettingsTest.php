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
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Support\Facades\DB;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $staff;
    protected $owner;
    protected $defaultOutlet;
    protected $outletSudirman;
    protected $gramUnit;
    protected $kgUnit;

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

        // Seed units
        $this->gramUnit = Unit::create(['name' => 'Gram', 'abbreviation' => 'gram']);
        $this->kgUnit = Unit::create(['name' => 'Kilogram', 'abbreviation' => 'kg']);

        // Seed unit conversions
        UnitConversion::create([
            'from_unit_id' => $this->kgUnit->id,
            'to_unit_id' => $this->gramUnit->id,
            'factor' => 1000.0000
        ]);

        // Seed outlets
        $this->defaultOutlet = Outlet::create([
            'name' => 'Gudang Utama',
            'address' => 'Jl. Sudirman No. 1',
            'phone' => '08111111',
            'is_default' => true
        ]);

        $this->outletSudirman = Outlet::create([
            'name' => 'Outlet Sudirman',
            'address' => 'Jl. Sudirman No. 10',
            'phone' => '08222222',
            'is_default' => false
        ]);
    }

    public function test_web_outlet_index_requires_auth()
    {
        $response = $this->get('/outlets');
        $response->assertRedirect('/login');
    }

    public function test_web_outlet_store_restricted_by_role()
    {
        // Owner cannot store
        $response = $this->actingAs($this->owner)->post('/outlets', [
            'name' => 'Outlet Baru',
        ]);
        $response->assertStatus(403);

        // Staff can store
        $response = $this->actingAs($this->staff)->post('/outlets', [
            'name' => 'Outlet Bandung',
            'phone' => '089999',
        ]);
        $response->assertRedirect('/outlets');
        $this->assertDatabaseHas('outlets', ['name' => 'Outlet Bandung']);
    }

    public function test_web_unit_index_and_store()
    {
        $response = $this->actingAs($this->staff)->post('/units', [
            'name' => 'Box',
            'abbreviation' => 'box',
        ]);
        $response->assertRedirect('/units');
        $this->assertDatabaseHas('units', ['abbreviation' => 'box']);
    }

    public function test_po_receiving_with_unit_conversion_and_outlet_stock()
    {
        // 1. Create ingredient with base unit "gram"
        $espresso = Ingredient::create([
            'name' => 'Espresso Beans',
            'unit' => 'gram',
            'unit_id' => $this->gramUnit->id,
            'stock' => 0,
            'minimum_stock' => 100,
        ]);

        // Set initial stock in default outlet
        $this->defaultOutlet->ingredients()->attach($espresso->id, ['stock' => 100.00]);
        $espresso->update(['stock' => 100.00]);

        // Create a supplier
        $supplier = Supplier::create(['name' => 'Kopi Supplier']);

        // 2. Store PO: Ordered 5 kg (kilogram) of Espresso Beans, to be received at Outlet Sudirman
        $response = $this->actingAs($this->staff)->post('/purchase-orders', [
            'supplier_id' => $supplier->id,
            'outlet_id' => $this->outletSudirman->id,
            'items' => [
                [
                    'ingredient_id' => $espresso->id,
                    'unit_id' => $this->kgUnit->id, // Purchasing in KG
                    'quantity' => 5.00, // 5 kg
                    'unit_price' => 50000.00,
                ]
            ]
        ]);

        $response->assertRedirect('/purchase-orders');

        $po = PurchaseOrder::first();
        $this->assertNotNull($po);
        $this->assertEquals($this->outletSudirman->id, $po->outlet_id);

        // 3. Receive PO: Should convert 5 kg to 5000 grams and add it to Outlet Sudirman's stock
        $response = $this->actingAs($this->admin)->post("/purchase-orders/{$po->id}/receive");
        $response->assertRedirect("/purchase-orders/{$po->id}");

        // Check Outlet Sudirman stock (should be 5000g)
        $sudirmanPivot = DB::table('outlet_ingredient')
            ->where('ingredient_id', $espresso->id)
            ->where('outlet_id', $this->outletSudirman->id)
            ->first();
        $this->assertNotNull($sudirmanPivot);
        $this->assertEquals(5000.00, $sudirmanPivot->stock);

        // Check Default Outlet stock (should remain 100g)
        $defaultPivot = DB::table('outlet_ingredient')
            ->where('ingredient_id', $espresso->id)
            ->where('outlet_id', $this->defaultOutlet->id)
            ->first();
        $this->assertEquals(100.00, $defaultPivot->stock);

        // Check cached total stock in ingredients table (should be 5100g: 100 + 5000)
        $this->assertEquals(5100.00, $espresso->fresh()->stock);
    }
}
