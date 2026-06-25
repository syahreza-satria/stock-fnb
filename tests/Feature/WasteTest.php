<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Waste;
use App\Models\StockMovement;
use App\Models\Outlet;

class WasteTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $staff;
    protected $owner;
    protected $ingredient;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
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

        // Seed ingredient
        $this->ingredient = Ingredient::create([
            'name' => 'Milk',
            'unit' => 'ml',
            'stock' => 1000.00,
            'minimum_stock' => 100.00,
        ]);

        // Seed default outlet
        $defaultOutlet = Outlet::create([
            'name' => 'Gudang Utama',
            'is_default' => true,
        ]);

        $defaultOutlet->ingredients()->attach($this->ingredient->id, [
            'stock' => 1000.00,
            'minimum_stock' => 100.00,
        ]);
    }

    public function test_web_waste_index_requires_auth()
    {
        $response = $this->get('/wastes');
        $response->assertRedirect('/login');
    }

    public function test_web_waste_index_accessible_to_any_authenticated_user()
    {
        // Admin
        $response = $this->actingAs($this->admin)->get('/wastes');
        $response->assertStatus(200);

        // Staff
        $response = $this->actingAs($this->staff)->get('/wastes');
        $response->assertStatus(200);

        // Owner
        $response = $this->actingAs($this->owner)->get('/wastes');
        $response->assertStatus(200);
    }

    public function test_web_waste_store_requires_auth()
    {
        $response = $this->post('/wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 100,
            'reason' => 'Kadaluarsa',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_web_waste_store_allowed_for_admin_and_staff()
    {
        // Test with staff
        $response = $this->actingAs($this->staff)->post('/wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 150.00,
            'reason' => 'Kadaluarsa',
            'description' => 'Susu basi di kulkas',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check DB
        $this->assertDatabaseHas('wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 150.00,
            'reason' => 'Kadaluarsa',
            'description' => 'Susu basi di kulkas',
            'user_id' => $this->staff->id,
        ]);

        // Check stock movement
        $this->assertDatabaseHas('stock_movements', [
            'ingredient_id' => $this->ingredient->id,
            'type' => 'out',
            'quantity' => 150.00,
            'description' => 'Pembuangan: Kadaluarsa (Susu basi di kulkas)',
        ]);

        // Check remaining stock
        $this->assertEquals(850.00, $this->ingredient->fresh()->stock);
    }

    public function test_web_waste_store_forbidden_for_owner()
    {
        $response = $this->actingAs($this->owner)->post('/wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 50,
            'reason' => 'Tumpah',
        ]);

        // Checks Middleware role returns 403
        $response->assertStatus(403);
    }

    public function test_web_waste_store_fails_when_quantity_exceeds_stock()
    {
        $response = $this->actingAs($this->admin)->from('/wastes')->post('/wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 1200.00, // Stock is 1000
            'reason' => 'Rusak / Basi',
        ]);

        $response->assertRedirect('/wastes');
        $response->assertSessionHas('error');
        $this->assertEquals(1000.00, $this->ingredient->fresh()->stock);
    }

    // --- API TESTS ---

    public function test_api_waste_index_requires_auth()
    {
        $response = $this->getJson('/api/wastes');
        $response->assertStatus(401);
    }

    public function test_api_waste_index_success_with_auth()
    {
        // Log a waste first
        Waste::create([
            'ingredient_id' => $this->ingredient->id,
            'user_id' => $this->admin->id,
            'quantity' => 50,
            'reason' => 'Tumpah',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/wastes');
        $response->assertStatus(200)
                 ->assertJsonCount(1)
                 ->assertJsonFragment([
                     'quantity' => '50.00',
                     'reason' => 'Tumpah',
                 ]);
    }

    public function test_api_waste_store_requires_admin_or_staff()
    {
        // Owner should get 403
        $response = $this->actingAs($this->owner, 'sanctum')->postJson('/api/wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 50,
            'reason' => 'Tumpah',
        ]);
        $response->assertStatus(403);

        // Staff should be allowed
        $response = $this->actingAs($this->staff, 'sanctum')->postJson('/api/wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 100.00,
            'reason' => 'Rusak / Basi',
            'description' => 'Pecah botol',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'waste']);

        $this->assertEquals(900.00, $this->ingredient->fresh()->stock);
    }

    public function test_api_waste_store_fails_insufficient_stock()
    {
        $response = $this->actingAs($this->staff, 'sanctum')->postJson('/api/wastes', [
            'ingredient_id' => $this->ingredient->id,
            'quantity' => 1500.00,
            'reason' => 'Tumpah',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message']);
    }
}
