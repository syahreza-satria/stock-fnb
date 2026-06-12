<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ApiReportController extends Controller
{
    /**
     * Retrieve general dashboard statistics.
     */
    public function dashboard()
    {
        $totalIngredients = Ingredient::count();
        $totalRecipes = Recipe::count();
        
        $lowStockIngredients = Ingredient::whereColumn('stock', '<=', 'minimum_stock')->get();
        $lowStockCount = $lowStockIngredients->count();

        return response()->json([
            'total_ingredients' => $totalIngredients,
            'total_recipes' => $totalRecipes,
            'low_stock_count' => $lowStockCount,
            'low_stock_ingredients' => $lowStockIngredients
        ]);
    }

    /**
     * Retrieve stock movements with filters.
     */
    public function movements(Request $request)
    {
        $query = StockMovement::with('ingredient');

        if ($request->filled('ingredient_id')) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        return response()->json($movements);
    }
}
