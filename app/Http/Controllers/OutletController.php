<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use Illuminate\Support\Facades\DB;

class OutletController extends Controller
{
    /**
     * Tampilkan daftar outlet.
     */
    public function index()
    {
        $outlets = Outlet::all();
        return view('outlets.index', compact('outlets'));
    }

    /**
     * Simpan outlet baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {
            $isDefault = $request->has('is_default') || $request->is_default;

            if ($isDefault) {
                Outlet::where('is_default', true)->update(['is_default' => false]);
            }

            if (Outlet::count() === 0) {
                $isDefault = true;
            }

            Outlet::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'is_default' => $isDefault,
            ]);
        });

        return redirect()->route('outlets.index')->with('success', 'Outlet/Gudang berhasil ditambahkan!');
    }

    /**
     * Perbarui outlet.
     */
    public function update(Request $request, Outlet $outlet)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $outlet) {
            $isDefault = $request->has('is_default') || $request->is_default;

            if ($isDefault && !$outlet->is_default) {
                Outlet::where('is_default', true)->update(['is_default' => false]);
            }

            if (!$isDefault && $outlet->is_default) {
                $isDefault = true; // Tetap default jika tidak ada default lain
            }

            $outlet->update([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'is_default' => $isDefault,
            ]);
        });

        return redirect()->route('outlets.index')->with('success', 'Outlet/Gudang berhasil diperbarui!');
    }

    /**
     * Hapus outlet.
     */
    public function destroy(Outlet $outlet)
    {
        if ($outlet->is_default) {
            return redirect()->back()->with('error', 'Outlet/Gudang default tidak dapat dihapus!');
        }

        $outlet->delete();
        return redirect()->route('outlets.index')->with('success', 'Outlet/Gudang berhasil dihapus!');
    }
}
