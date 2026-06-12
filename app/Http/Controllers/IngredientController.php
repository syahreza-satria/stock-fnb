<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::all();
        return view('ingredients.index', compact('ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $ingredient = Ingredient::create($request->only('name', 'unit', 'stock', 'minimum_stock'));

            // Log initial stock if greater than 0
            if ($ingredient->stock > 0) {
                StockMovement::create([
                    'ingredient_id' => $ingredient->id,
                    'type' => 'in',
                    'quantity' => $ingredient->stock,
                    'description' => 'Stok awal saat penginputan bahan baku',
                ]);
            }
        });

        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $ingredient->update($request->only('name', 'unit', 'minimum_stock'));

        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil diperbarui!');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil dihapus!');
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
            return redirect()->back()->with('error', 'Stok tidak mencukupi untuk operasi ini!');
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
                'description' => $request->description ?? 'Penyesuaian manual',
            ]);
        });

        return redirect()->route('ingredients.index')->with('success', 'Stok berhasil disesuaikan!');
    }
}
