<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    /**
     * Tampilkan halaman Analitik Lanjutan.
     */
    public function index()
    {
        $recipes = Recipe::with('ingredients')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        $outlets = Outlet::orderBy('name')->get();

        // 1. Analisa Food Cost & Margin
        $foodCostAnalysis = [];
        foreach ($recipes as $recipe) {
            $totalCost = 0;
            foreach ($recipe->ingredients as $ing) {
                $totalCost += $ing->pivot->quantity * $ing->cost_price;
            }
            $margin = $recipe->selling_price - $totalCost;
            $marginPercentage = $recipe->selling_price > 0 ? ($margin / $recipe->selling_price) * 100 : 0;
            $foodCostPercentage = $recipe->selling_price > 0 ? ($totalCost / $recipe->selling_price) * 100 : 0;

            $foodCostAnalysis[] = [
                'recipe' => $recipe,
                'total_cost' => $totalCost,
                'margin' => $margin,
                'margin_percentage' => $marginPercentage,
                'food_cost_percentage' => $foodCostPercentage
            ];
        }

        // 2. Dead Stock Report (Item lambat bergerak)
        // Definisi: Bahan baku yang tidak memiliki transaksi "out" (pemakaian/penjualan) dalam 30 hari terakhir.
        $thirtyDaysAgo = now()->subDays(30);
        
        $activeIngredientIds = StockMovement::where('type', 'out')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->pluck('ingredient_id')
            ->unique()
            ->toArray();

        $deadStock = Ingredient::whereNotIn('id', $activeIngredientIds)
            ->with(['unitRelation'])
            ->get();

        // 3. Tren Konsumsi Bahan Musiman
        // Mengelompokkan total pemakaian (type = out) per bahan baku berdasarkan bulan-tahun
        $monthlyConsumption = DB::table('stock_movements')
            ->join('ingredients', 'stock_movements.ingredient_id', '=', 'ingredients.id')
            ->where('stock_movements.type', 'out')
            ->select(
                'ingredients.name as ingredient_name',
                DB::raw('YEAR(stock_movements.created_at) as year'),
                DB::raw('MONTH(stock_movements.created_at) as month'),
                DB::raw('SUM(stock_movements.quantity) as total_qty')
            )
            ->groupBy('ingredient_name', 'year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // 4. Variance Report (Selisih stok teoritis vs aktual)
        $stockTakes = DB::table('stock_takes')
            ->join('outlets', 'stock_takes.outlet_id', '=', 'outlets.id')
            ->join('ingredients', 'stock_takes.ingredient_id', '=', 'ingredients.id')
            ->join('users', 'stock_takes.user_id', '=', 'users.id')
            ->select(
                'stock_takes.*',
                'outlets.name as outlet_name',
                'ingredients.name as ingredient_name',
                'ingredients.unit as ingredient_unit',
                'users.name as user_name'
            )
            ->orderBy('stock_takes.created_at', 'desc')
            ->get();

        return view('reports.analytics', compact(
            'recipes',
            'ingredients',
            'outlets',
            'foodCostAnalysis',
            'deadStock',
            'monthlyConsumption',
            'stockTakes'
        ));
    }

    /**
     * Simpan Stock Take (Pencatatan Stok Aktual untuk hitung Variance).
     */
    public function storeStockTake(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'actual_stock' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $outletId = $request->outlet_id;
        $ingredientId = $request->ingredient_id;
        $actualStock = $request->actual_stock;

        // Dapatkan stok teoritis dari database
        $pivot = DB::table('outlet_ingredient')
            ->where('outlet_id', $outletId)
            ->where('ingredient_id', $ingredientId)
            ->first();

        $theoreticalStock = $pivot ? $pivot->stock : 0;
        $variance = $actualStock - $theoreticalStock;

        // Catat di tabel stock_takes
        DB::table('stock_takes')->insert([
            'outlet_id' => $outletId,
            'ingredient_id' => $ingredientId,
            'theoretical_stock' => $theoreticalStock,
            'actual_stock' => $actualStock,
            'variance' => $variance,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Opsional: Sesuaikan stok teoritis sistem agar cocok dengan hasil fisik aktual
        $ingredient = Ingredient::findOrFail($ingredientId);
        if ($variance != 0) {
            $adjustType = $variance > 0 ? 'in' : 'out';
            $adjustQty = abs($variance);
            
            $ingredient->adjustStock(
                $outletId,
                $adjustType,
                $adjustQty,
                "Stock Opname (Selisih Aktual vs Teoritis)"
            );
        }

        return redirect()->back()->with('success', 'Stock opname berhasil dicatat! Selisih stok telah disesuaikan.');
    }

    /**
     * Perbarui harga jual menu dan harga modal bahan baku.
     */
    public function updatePricing(Request $request)
    {
        $request->validate([
            'recipe_prices' => 'nullable|array',
            'recipe_prices.*.id' => 'required|exists:recipes,id',
            'recipe_prices.*.selling_price' => 'required|numeric|min:0',
            'ingredient_costs' => 'nullable|array',
            'ingredient_costs.*.id' => 'required|exists:ingredients,id',
            'ingredient_costs.*.cost_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->filled('recipe_prices')) {
                foreach ($request->recipe_prices as $p) {
                    Recipe::where('id', $p['id'])->update(['selling_price' => $p['selling_price']]);
                }
            }

            if ($request->filled('ingredient_costs')) {
                foreach ($request->ingredient_costs as $c) {
                    Ingredient::where('id', $c['id'])->update(['cost_price' => $c['cost_price']]);
                }
            }
        });

        return redirect()->back()->with('success', 'Data harga jual & harga modal berhasil diperbarui!');
    }
}
