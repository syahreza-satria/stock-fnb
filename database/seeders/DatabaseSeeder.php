<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\StockMovement;
use App\Models\Outlet;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign keys to safely truncate tables
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Ingredient::truncate();
        Recipe::truncate();
        StockMovement::truncate();
        Outlet::truncate();
        Unit::truncate();
        UnitConversion::truncate();
        DB::table('outlet_ingredient')->truncate();
        Schema::enableForeignKeyConstraints();

        // Create Users
        User::create([
            'name' => 'Admin F&B',
            'email' => 'admin@fnb.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff F&B',
            'email' => 'staff@fnb.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'Owner F&B',
            'email' => 'owner@fnb.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        // Create Units
        $g = Unit::create(['name' => 'Gram', 'abbreviation' => 'gram']);
        $ml = Unit::create(['name' => 'Mililiter', 'abbreviation' => 'ml']);
        $pcs = Unit::create(['name' => 'Pcs', 'abbreviation' => 'pcs']);
        $kg = Unit::create(['name' => 'Kilogram', 'abbreviation' => 'kg']);
        $l = Unit::create(['name' => 'Liter', 'abbreviation' => 'l']);
        $box = Unit::create(['name' => 'Box', 'abbreviation' => 'box']);

        // Create conversions
        UnitConversion::create(['from_unit_id' => $kg->id, 'to_unit_id' => $g->id, 'factor' => 1000.0000]);
        UnitConversion::create(['from_unit_id' => $l->id, 'to_unit_id' => $ml->id, 'factor' => 1000.0000]);

        // Create Outlets
        $gudangUtama = Outlet::create([
            'name' => 'Gudang Utama',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'phone' => '081111111',
            'is_default' => true,
        ]);

        $outletSudirman = Outlet::create([
            'name' => 'Outlet Sudirman',
            'address' => 'Jl. Sudirman No. 10, Jakarta',
            'phone' => '082222222',
            'is_default' => false,
        ]);

        // Create Ingredients
        $espresso = Ingredient::create([
            'name' => 'Espresso Beans',
            'unit' => 'gram',
            'unit_id' => $g->id,
            'stock' => 1000.00,
            'minimum_stock' => 200.00,
            'cost_price' => 150.00, // Rp 150 per gram
        ]);

        $milk = Ingredient::create([
            'name' => 'Fresh Milk',
            'unit' => 'ml',
            'unit_id' => $ml->id,
            'stock' => 2000.00,
            'minimum_stock' => 500.00,
            'cost_price' => 20.00, // Rp 20 per ml
        ]);

        $caramel = Ingredient::create([
            'name' => 'Caramel Syrup',
            'unit' => 'ml',
            'unit_id' => $ml->id,
            'stock' => 500.00,
            'minimum_stock' => 100.00,
            'cost_price' => 50.00, // Rp 50 per ml
        ]);

        $cup = Ingredient::create([
            'name' => 'Paper Cup 12oz',
            'unit' => 'pcs',
            'unit_id' => $pcs->id,
            'stock' => 10.00,
            'minimum_stock' => 20.00,
            'cost_price' => 1500.00, // Rp 1500 per cup
        ]);

        // Seed stocks into outlets
        $gudangUtama->ingredients()->attach([
            $espresso->id => ['stock' => 1000.00, 'minimum_stock' => 200.00],
            $milk->id => ['stock' => 2000.00, 'minimum_stock' => 500.00],
            $caramel->id => ['stock' => 500.00, 'minimum_stock' => 100.00],
            $cup->id => ['stock' => 10.00, 'minimum_stock' => 20.00],
        ]);

        // Create Initial Stock Movements
        StockMovement::create([
            'ingredient_id' => $espresso->id,
            'outlet_id' => $gudangUtama->id,
            'type' => 'in',
            'quantity' => 1000.00,
            'description' => 'Initial seed stock',
        ]);
        StockMovement::create([
            'ingredient_id' => $milk->id,
            'outlet_id' => $gudangUtama->id,
            'type' => 'in',
            'quantity' => 2000.00,
            'description' => 'Initial seed stock',
        ]);
        StockMovement::create([
            'ingredient_id' => $caramel->id,
            'outlet_id' => $gudangUtama->id,
            'type' => 'in',
            'quantity' => 500.00,
            'description' => 'Initial seed stock',
        ]);
        StockMovement::create([
            'ingredient_id' => $cup->id,
            'outlet_id' => $gudangUtama->id,
            'type' => 'in',
            'quantity' => 10.00,
            'description' => 'Initial seed stock',
        ]);

        // Create Recipes
        $latte = Recipe::create(['name' => 'Caffe Latte', 'selling_price' => 32000.00]);
        $latte->ingredients()->attach([
            $espresso->id => ['quantity' => 18.00],
            $milk->id => ['quantity' => 200.00],
            $cup->id => ['quantity' => 1.00],
        ]);

        $macchiato = Recipe::create(['name' => 'Caramel Macchiato', 'selling_price' => 38000.00]);
        $macchiato->ingredients()->attach([
            $espresso->id => ['quantity' => 18.00],
            $milk->id => ['quantity' => 180.00],
            $caramel->id => ['quantity' => 15.00],
            $cup->id => ['quantity' => 1.00],
        ]);
    }
}
