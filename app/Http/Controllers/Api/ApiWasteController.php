<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Waste;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiWasteController extends Controller
{
    /**
     * Get all waste records.
     */
    public function index()
    {
        $wastes = Waste::with(['ingredient', 'user'])->orderBy('created_at', 'desc')->get();
        return response()->json($wastes);
    }

    /**
     * Record a new waste event.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|gt:0',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $ingredient = Ingredient::findOrFail($request->ingredient_id);
        $quantity = $request->quantity;

        if ($ingredient->stock < $quantity) {
            return response()->json([
                'message' => 'Stok tidak mencukupi untuk melakukan pembuangan sebesar ' . number_format($quantity, 2) . ' ' . $ingredient->unit . '!'
            ], 422);
        }

        $waste = DB::transaction(function () use ($ingredient, $quantity, $request) {
            // 1. Kurangi stok bahan baku
            $ingredient->decrement('stock', $quantity);

            // 2. Buat record Waste
            $w = Waste::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => Auth::id(),
                'quantity' => $quantity,
                'reason' => $request->reason,
                'description' => $request->description,
            ]);

            // 3. Catat pergerakan stok (out)
            StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'type' => 'out',
                'quantity' => $quantity,
                'description' => 'Pembuangan: ' . $request->reason . ($request->description ? ' (' . $request->description . ')' : ''),
            ]);

            return $w;
        });

        return response()->json([
            'message' => 'Catatan pembuangan bahan baku berhasil disimpan!',
            'waste' => $waste->load(['ingredient', 'user'])
        ], 201);
    }
}
