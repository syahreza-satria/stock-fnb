<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\UnitConversion;

class UnitController extends Controller
{
    /**
     * Tampilkan daftar satuan dan aturan konversi.
     */
    public function index()
    {
        $units = Unit::all();
        $conversions = UnitConversion::with(['fromUnit', 'toUnit'])->get();
        return view('units.index', compact('units', 'conversions'));
    }

    /**
     * Simpan satuan baru.
     */
    public function storeUnit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50|unique:units,abbreviation',
        ]);

        Unit::create($request->only('name', 'abbreviation'));

        return redirect()->route('units.index')->with('success', 'Satuan baru berhasil ditambahkan!');
    }

    /**
     * Simpan aturan konversi baru.
     */
    public function storeConversion(Request $request)
    {
        $request->validate([
            'from_unit_id' => 'required|exists:units,id|different:to_unit_id',
            'to_unit_id' => 'required|exists:units,id',
            'factor' => 'required|numeric|gt:0',
        ]);

        $exists = UnitConversion::where('from_unit_id', $request->from_unit_id)
                                ->where('to_unit_id', $request->to_unit_id)
                                ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Aturan konversi tersebut sudah terdaftar!');
        }

        UnitConversion::create($request->only('from_unit_id', 'to_unit_id', 'factor'));

        return redirect()->route('units.index')->with('success', 'Aturan konversi berhasil ditambahkan!');
    }

    /**
     * Hapus aturan konversi.
     */
    public function destroyConversion(UnitConversion $conversion)
    {
        $conversion->delete();
        return redirect()->route('units.index')->with('success', 'Aturan konversi berhasil dihapus!');
    }
}
