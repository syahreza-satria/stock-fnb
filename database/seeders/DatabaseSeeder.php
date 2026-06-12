<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        // Create Ingredients
        $espresso = Ingredient::create([
            'name' => 'Espresso Beans',
            'unit' => 'gram',
            'stock' => 1000.00,
            'minimum_stock' => 200.00,
        ]);

        $milk = Ingredient::create([
            'name' => 'Fresh Milk',
            'unit' => 'ml',
            'stock' => 2000.00,
            'minimum_stock' => 500.00,
        ]);

        $caramel = Ingredient::create([
            'name' => 'Caramel Syrup',
            'unit' => 'ml',
            'stock' => 500.00,
            'minimum_stock' => 100.00,
        ]);

        $cup = Ingredient::create([
            'name' => 'Paper Cup 12oz',
            'unit' => 'pcs',
            'stock' => 10.00, // low stock purposefully for alert testing
            'minimum_stock' => 20.00,
        ]);

        // Create Initial Stock Movements
        StockMovement::create([
            'ingredient_id' => $espresso->id,
            'type' => 'in',
            'quantity' => 1000.00,
            'description' => 'Initial seed stock',
        ]);
        StockMovement::create([
            'ingredient_id' => $milk->id,
            'type' => 'in',
            'quantity' => 2000.00,
            'description' => 'Initial seed stock',
        ]);
        StockMovement::create([
            'ingredient_id' => $caramel->id,
            'type' => 'in',
            'quantity' => 500.00,
            'description' => 'Initial seed stock',
        ]);
        StockMovement::create([
            'ingredient_id' => $cup->id,
            'type' => 'in',
            'quantity' => 10.00,
            'description' => 'Initial seed stock',
        ]);

        // Create Recipes
        $latte = Recipe::create(['name' => 'Caffe Latte']);
        $latte->ingredients()->attach([
            $espresso->id => ['quantity' => 18.00],
            $milk->id => ['quantity' => 200.00],
            $cup->id => ['quantity' => 1.00],
        ]);

        $macchiato = Recipe::create(['name' => 'Caramel Macchiato']);
        $macchiato->ingredients()->attach([
            $espresso->id => ['quantity' => 18.00],
            $milk->id => ['quantity' => 180.00],
            $caramel->id => ['quantity' => 15.00],
            $cup->id => ['quantity' => 1.00],
        ]);
    }
}
