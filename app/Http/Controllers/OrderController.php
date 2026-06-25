<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\StockMovement;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Tampilkan halaman simulasi penjualan.
     */
    public function index()
    {
        $recipes = Recipe::with('ingredients')->get();
        return view('orders.index', compact('recipes'));
    }

    /**
     * Proses simulasi penjualan menu.
     */
    public function processSale(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $recipe = Recipe::with('ingredients')->findOrFail($request->recipe_id);
        $orderQty = $request->quantity;

        // Dapatkan default outlet
        $defaultOutlet = Outlet::where('is_default', true)->first() ?: Outlet::first();

        if (!$defaultOutlet) {
            return redirect()->back()->with('error', 'Tidak ada outlet default yang dikonfigurasi!');
        }

        // 1. Fase validasi: Cek apakah bahan baku memiliki stok yang cukup di outlet default
        $insufficientIngredients = [];
        foreach ($recipe->ingredients as $ingredient) {
            $requiredQty = $ingredient->pivot->quantity * $orderQty;

            $pivot = DB::table('outlet_ingredient')
                ->where('ingredient_id', $ingredient->id)
                ->where('outlet_id', $defaultOutlet->id)
                ->first();

            $outletStock = $pivot ? $pivot->stock : 0;

            if ($outletStock < $requiredQty) {
                $insufficientIngredients[] = sprintf(
                    "%s (Dibutuhkan: %.2f %s, Tersedia di %s: %.2f %s)",
                    $ingredient->name,
                    $requiredQty,
                    $ingredient->unit,
                    $defaultOutlet->name,
                    $outletStock,
                    $ingredient->unit
                );
            }
        }

        if (!empty($insufficientIngredients)) {
            return redirect()->back()->with('error', 'Stok bahan baku tidak mencukupi: ' . implode(', ', $insufficientIngredients));
        }

        // 2. Fase eksekusi: Kurangi stok di outlet default dan catat pergerakan
        DB::transaction(function () use ($recipe, $orderQty, $defaultOutlet) {
            foreach ($recipe->ingredients as $ingredient) {
                $deductQty = $ingredient->pivot->quantity * $orderQty;
                
                $ingredient->adjustStock(
                    $defaultOutlet->id,
                    'out',
                    $deductQty,
                    "Penjualan Menu: {$recipe->name} (Jumlah: {$orderQty})"
                );
            }
        });

        return redirect()->route('orders.index')->with('success', "Simulasi penjualan {$orderQty}x {$recipe->name} berhasil diproses!");
    }
}
