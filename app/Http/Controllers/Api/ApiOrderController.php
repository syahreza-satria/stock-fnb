<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiOrderController extends Controller
{
    public function processSale(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $recipe = Recipe::with('ingredients')->findOrFail($request->recipe_id);
        $orderQty = $request->quantity;

        // 1. Validation phase: Check if all ingredients have enough stock
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
            return response()->json([
                'message' => 'Stok bahan baku tidak mencukupi',
                'insufficient_ingredients' => $insufficientIngredients
            ], 422);
        }

        // 2. Execution phase: Decrement stock and record movement
        DB::transaction(function () use ($recipe, $orderQty) {
            foreach ($recipe->ingredients as $ingredient) {
                $deductQty = $ingredient->pivot->quantity * $orderQty;
                
                // Decrement stock
                $ingredient->decrement('stock', $deductQty);

                // Log stock movement
                StockMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'type' => 'out',
                    'quantity' => $deductQty,
                    'description' => "Penjualan Menu: {$recipe->name} (Jumlah: {$orderQty}) via API",
                ]);
            }
        });

        return response()->json([
            'message' => "Simulasi penjualan {$orderQty}x {$recipe->name} berhasil diproses!"
        ]);
    }
}
