<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Waste;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WasteController extends Controller
{
    /**
     * Tampilkan halaman daftar pembuangan bahan baku.
     */
    public function index()
    {
        $wastes = Waste::with(['ingredient', 'user', 'outlet'])->orderBy('created_at', 'desc')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        $outlets = Outlet::orderBy('name')->get();

        return view('wastes.index', compact('wastes', 'ingredients', 'outlets'));
    }

    /**
     * Catat pembuangan bahan baku baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'outlet_id' => 'nullable|exists:outlets,id',
            'quantity' => 'required|numeric|gt:0',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $ingredient = Ingredient::findOrFail($request->ingredient_id);
        $outletId = $request->outlet_id ?? (Outlet::where('is_default', true)->value('id') ?? Outlet::value('id'));
        $quantity = $request->quantity;

        // Cek stok di outlet tersebut
        $pivot = DB::table('outlet_ingredient')
            ->where('ingredient_id', $ingredient->id)
            ->where('outlet_id', $outletId)
            ->first();

        $currentStock = $pivot ? $pivot->stock : 0;

        if ($currentStock < $quantity) {
            $outlet = Outlet::findOrFail($outletId);
            return redirect()->back()->with('error', 'Stok pada outlet ' . $outlet->name . ' tidak mencukupi untuk melakukan pembuangan sebesar ' . number_format($quantity, 2) . ' ' . $ingredient->unit . '!');
        }

        DB::transaction(function () use ($ingredient, $outletId, $quantity, $request) {
            // 1. Kurangi stok bahan baku pada outlet terpilih (dan log pergerakan stok)
            $ingredient->adjustStock(
                $outletId,
                'out',
                $quantity,
                'Pembuangan: ' . $request->reason . ($request->description ? ' (' . $request->description . ')' : '')
            );

            // 2. Buat record Waste
            Waste::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => Auth::id(),
                'outlet_id' => $outletId,
                'quantity' => $quantity,
                'reason' => $request->reason,
                'description' => $request->description,
            ]);
        });

        return redirect()->route('wastes.index')->with('success', 'Catatan pembuangan bahan baku berhasil disimpan!');
    }
}
