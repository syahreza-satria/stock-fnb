<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiIngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::all();
        return response()->json($ingredients);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $ingredient = DB::transaction(function () use ($request) {
            $ing = Ingredient::create($request->only('name', 'unit', 'stock', 'minimum_stock'));

            if ($ing->stock > 0) {
                StockMovement::create([
                    'ingredient_id' => $ing->id,
                    'type' => 'in',
                    'quantity' => $ing->stock,
                    'description' => 'Stok awal saat penginputan bahan baku',
                ]);
            }

            return $ing;
        });

        return response()->json($ingredient, 201);
    }

    public function show(Ingredient $ingredient)
    {
        return response()->json($ingredient);
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $ingredient->update($request->only('name', 'unit', 'minimum_stock'));

        return response()->json($ingredient);
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return response()->json([
            'message' => 'Ingredient deleted successfully'
        ]);
    }

    public function adjustStock(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:255',
        ]);

        $type = $request->type;
        $qty = $request->quantity;

        if ($type === 'out' && $ingredient->stock < $qty) {
            return response()->json([
                'message' => 'Stok tidak mencukupi untuk operasi ini!'
            ], 422);
        }

        DB::transaction(function () use ($ingredient, $type, $qty, $request) {
            if ($type === 'in') {
                $ingredient->increment('stock', $qty);
            } else {
                $ingredient->decrement('stock', $qty);
            }

            StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => $type,
                'quantity' => $qty,
                'description' => $request->description ?? 'Penyesuaian manual via API',
            ]);
        });

        return response()->json([
            'message' => 'Stok berhasil disesuaikan!',
            'ingredient' => $ingredient->fresh(),
        ]);
    }
}
