<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\Outlet;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    /**
     * Tampilkan daftar bahan baku.
     */
    public function index()
    {
        $ingredients = Ingredient::with(['unitRelation', 'outlets'])->orderBy('name')->get();
        $outlets = Outlet::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('ingredients.index', compact('ingredients', 'outlets', 'units'));
    }

    /**
     * Simpan bahan baku baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $unit = Unit::findOrFail($request->unit_id);
            
            $ingredient = Ingredient::create([
                'name' => $request->name,
                'unit' => $unit->abbreviation,
                'unit_id' => $unit->id,
                'stock' => 0, // Akan di-update via adjustStock
                'minimum_stock' => $request->minimum_stock,
            ]);

            // Dapatkan default outlet
            $defaultOutlet = Outlet::where('is_default', true)->first() ?: Outlet::first();

            if ($defaultOutlet) {
                // Inisialisasi stok di outlet default
                $ingredient->adjustStock(
                    $defaultOutlet->id,
                    'in',
                    $request->stock,
                    'Stok awal saat penginputan bahan baku'
                );
            }
        });

        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * Perbarui bahan baku.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,id',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $unit = Unit::findOrFail($request->unit_id);

        $ingredient->update([
            'name' => $request->name,
            'unit' => $unit->abbreviation,
            'unit_id' => $unit->id,
            'minimum_stock' => $request->minimum_stock,
        ]);

        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil diperbarui!');
    }

    /**
     * Hapus bahan baku.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return redirect()->route('ingredients.index')->with('success', 'Bahan baku berhasil dihapus!');
    }

    /**
     * Sesuaikan stok bahan baku (manual).
     */
    public function adjustStock(Request $request, Ingredient $ingredient)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|gt:0',
            'description' => 'nullable|string|max:255',
        ]);

        $outletId = $request->outlet_id;
        $type = $request->type;
        $qty = $request->quantity;

        // Cek stok di outlet tersebut
        $pivot = DB::table('outlet_ingredient')
            ->where('ingredient_id', $ingredient->id)
            ->where('outlet_id', $outletId)
            ->first();

        $currentStock = $pivot ? $pivot->stock : 0;

        if ($type === 'out' && $currentStock < $qty) {
            return redirect()->back()->with('error', 'Stok pada outlet tersebut tidak mencukupi untuk operasi ini!');
        }

        DB::transaction(function () use ($ingredient, $outletId, $type, $qty, $request) {
            $ingredient->adjustStock(
                $outletId,
                $type,
                $qty,
                $request->description ?? 'Penyesuaian manual'
            );
        });

        return redirect()->route('ingredients.index')->with('success', 'Stok berhasil disesuaikan!');
    }
}
