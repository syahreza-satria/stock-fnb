<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\StockMovement;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama.
     */
    public function index()
    {
        $totalIngredients = Ingredient::count();
        $totalRecipes = Recipe::count();
        
        $lowStockIngredients = Ingredient::whereColumn('stock', '<=', 'minimum_stock')->get();
        $lowStockCount = $lowStockIngredients->count();

        // Data untuk Chart 1: Top 10 Stok Bahan Baku Terbanyak
        $chartIngredients = Ingredient::orderBy('stock', 'desc')->take(10)->get();
        
        // Data untuk Chart 2: Komposisi Pergerakan Stok (Masuk vs Keluar)
        $movementInCount = StockMovement::where('type', 'in')->count();
        $movementOutCount = StockMovement::where('type', 'out')->count();

        return view('dashboard', compact(
            'totalIngredients', 
            'totalRecipes', 
            'lowStockCount', 
            'lowStockIngredients',
            'chartIngredients',
            'movementInCount',
            'movementOutCount'
        ));
    }
}
