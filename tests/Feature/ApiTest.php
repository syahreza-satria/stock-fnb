<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Recipe;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic users
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

        // Seed basic ingredient
        $this->ingredient = Ingredient::create([
            'name' => 'Coffee Beans',
            'unit' => 'gram',
            'stock' => 100.00,
            'minimum_stock' => 10.00,
        ]);
    }

    public function test_api_login_fails_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJsonStructure(['message']);
    }

    public function test_api_login_success()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'user']);
    }

    public function test_api_ingredients_list_requires_auth()
    {
        $response = $this->getJson('/api/ingredients');
        $response->assertStatus(401);
    }

    public function test_api_ingredients_list_success_with_auth()
    {
        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/ingredients');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Coffee Beans',
                     'unit' => 'gram'
                 ]);
    }

    public function test_api_ingredients_adjust_stock_requires_admin_or_staff()
    {
        // Owner should be rejected with 403
        $response = $this->actingAs($this->owner, 'sanctum')
                         ->postJson("/api/ingredients/{$this->ingredient->id}/adjust", [
                             'type' => 'in',
                             'quantity' => 50,
                             'description' => 'Test restock'
                         ]);

        $response->assertStatus(403);

        // Staff should be allowed
        $response = $this->actingAs($this->staff, 'sanctum')
                         ->postJson("/api/ingredients/{$this->ingredient->id}/adjust", [
                             'type' => 'in',
                             'quantity' => 50,
                             'description' => 'Test restock'
                         ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'stock' => '150.00'
                 ]);
    }

    public function test_api_sell_order_success()
    {
        $recipe = Recipe::create(['name' => 'Black Coffee']);
        $recipe->ingredients()->attach($this->ingredient->id, ['quantity' => 15.00]);

        $response = $this->actingAs($this->staff, 'sanctum')
                         ->postJson('/api/orders/sell', [
                             'recipe_id' => $recipe->id,
                             'quantity' => 2,
                         ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Simulasi penjualan 2x Black Coffee berhasil diproses!'
                 ]);

        // Check remaining stock
        $this->assertEquals(70.00, $this->ingredient->fresh()->stock);
    }

    public function test_api_sell_order_insufficient_stock()
    {
        $recipe = Recipe::create(['name' => 'Black Coffee']);
        $recipe->ingredients()->attach($this->ingredient->id, ['quantity' => 15.00]);

        $response = $this->actingAs($this->staff, 'sanctum')
                         ->postJson('/api/orders/sell', [
                             'recipe_id' => $recipe->id,
                             'quantity' => 10, // Needs 150g, only has 100g
                         ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'insufficient_ingredients']);
    }
}

