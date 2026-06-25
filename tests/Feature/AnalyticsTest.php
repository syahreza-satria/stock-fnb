<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Outlet;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Units
        $this->g = Unit::create(['name' => 'Gram', 'abbreviation' => 'gram']);
        $this->ml = Unit::create(['name' => 'Mililiter', 'abbreviation' => 'ml']);

        // Create Outlet
        $this->outlet = Outlet::create([
            'name' => 'Gudang Utama',
            'address' => 'Jl. Test',
            'phone' => '123',
            'is_default' => true
        ]);

        // Create Ingredients
        $this->espresso = Ingredient::create([
            'name' => 'Espresso Beans',
            'unit' => 'gram',
            'unit_id' => $this->g->id,
            'stock' => 1000.00,
            'minimum_stock' => 200.00,
            'cost_price' => 150.00
        ]);

        // Attach stock to outlet
        $this->outlet->ingredients()->attach($this->espresso->id, [
            'stock' => 1000.00,
            'minimum_stock' => 200.00
        ]);

        // Create Recipe
        $this->latte = Recipe::create([
            'name' => 'Caffe Latte',
            'selling_price' => 32000.00
        ]);
        $this->latte->ingredients()->attach($this->espresso->id, ['quantity' => 18.00]);
    }

    public function test_analytics_page_requires_auth()
    {
        $response = $this->get(route('reports.analytics'));
        $response->assertRedirect(route('login'));
    }

    public function test_analytics_page_accessible_with_auth()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.analytics'));
        $response->assertStatus(200);
        $response->assertSee('Food Cost');
        $response->assertSee('Caffe Latte');
    }

    public function test_update_pricing()
    {
        $response = $this->actingAs($this->admin)->post(route('analytics.pricing.update'), [
            'recipe_prices' => [
                ['id' => $this->latte->id, 'selling_price' => 35000.00]
            ],
            'ingredient_costs' => [
                ['id' => $this->espresso->id, 'cost_price' => 160.00]
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertEquals(35000.00, $this->latte->fresh()->selling_price);
        $this->assertEquals(160.00, $this->espresso->fresh()->cost_price);
    }

    public function test_store_stock_take()
    {
        $response = $this->actingAs($this->admin)->post(route('analytics.stock-take.store'), [
            'outlet_id' => $this->outlet->id,
            'ingredient_id' => $this->espresso->id,
            'actual_stock' => 950.00,
            'notes' => 'Selisih 50gr tumpah'
        ]);

        $response->assertRedirect();

        // Check variance recorded
        $this->assertDatabaseHas('stock_takes', [
            'outlet_id' => $this->outlet->id,
            'ingredient_id' => $this->espresso->id,
            'theoretical_stock' => 1000.00,
            'actual_stock' => 950.00,
            'variance' => -50.00,
            'notes' => 'Selisih 50gr tumpah',
            'user_id' => $this->admin->id
        ]);

        // Verify stock adjusted correctly
        $this->assertEquals(950.00, $this->espresso->fresh()->stock);
    }
}
