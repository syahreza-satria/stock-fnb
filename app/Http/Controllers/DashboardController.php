<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Recipe;

class DashboardController extends Controller
{
    public function index()
    {
        $totalIngredients = Ingredient::count();
        $totalRecipes = Recipe::count();
        
        $lowStockIngredients = Ingredient::whereColumn('stock', '<=', 'minimum_stock')->get();
        $lowStockCount = $lowStockIngredients->count();

        return view('dashboard', compact('totalIngredients', 'totalRecipes', 'lowStockCount', 'lowStockIngredients'));
    }
}
