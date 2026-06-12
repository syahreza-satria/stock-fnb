<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('ingredients')->get();
        return view('orders.index', compact('recipes'));
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $recipe = Recipe::with('ingredients')->findOrFail($request->recipe_id);
        $orderQty = $request->quantity;

        // 1. Fase validasi: Cek apakah semua bahan baku memiliki stok yang cukup
        $insufficientIngredients = [];
        foreach ($recipe->ingredients as $ingredient) {
            $requiredQty = $ingredient->pivot->quantity * $orderQty;
            if ($ingredient->stock < $requiredQty) {
                $insufficientIngredients[] = sprintf(
                    "%s (Dibutuhkan: %.2f %s, Tersedia: %.2f %s)",
                    $ingredient->name,
                    $requiredQty,
                    $ingredient->unit,
                    $ingredient->stock,
                    $ingredient->unit
                );
            }
        }

        if (!empty($insufficientIngredients)) {
            return redirect()->back()->with('error', 'Stok bahan baku tidak mencukupi: ' . implode(', ', $insufficientIngredients));
        }

        // 2. Fase eksekusi: Kurangi stok dan catat pergerakan
        DB::transaction(function () use ($recipe, $orderQty) {
            foreach ($recipe->ingredients as $ingredient) {
                $deductQty = $ingredient->pivot->quantity * $orderQty;
                
                // Kurangi stok
                $ingredient->decrement('stock', $deductQty);

                // Catat pergerakan stok
                StockMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'type' => 'out',
                    'quantity' => $deductQty,
                    'description' => "Penjualan Menu: {$recipe->name} (Jumlah: {$orderQty})",
                ]);
            }
        });

        return redirect()->route('orders.index')->with('success', "Simulasi penjualan {$orderQty}x {$recipe->name} berhasil diproses!");
    }
}
