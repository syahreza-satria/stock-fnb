<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Ingredient;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\StockMovement;
use App\Models\Outlet;
use App\Models\Unit;
use App\Models\UnitConversion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * Tampilkan daftar Purchase Order (PO).
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user', 'outlet'])->orderBy('created_at', 'desc')->get();
        return view('purchase_orders.index', compact('purchaseOrders'));
    }

    /**
     * Tampilkan form pembuatan PO baru.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $ingredients = Ingredient::with('unitRelation')->orderBy('name')->get();
        $outlets = Outlet::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        
        return view('purchase_orders.create', compact('suppliers', 'ingredients', 'outlets', 'units'));
    }

    /**
     * Simpan Purchase Order baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'outlet_id' => 'nullable|exists:outlets,id',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $poNumber = 'PO-' . date('YmdHis') . '-' . rand(10, 99);
            
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $outletId = $request->outlet_id ?? (Outlet::where('is_default', true)->value('id') ?? Outlet::value('id'));

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $request->supplier_id,
                'outlet_id' => $outletId,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                $ing = Ingredient::find($item['ingredient_id']);
                $unitId = $item['unit_id'] ?? ($ing ? $ing->unit_id : null);

                PurchaseOrderDetail::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'unit_id' => $unitId,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order berhasil dibuat!');
    }

    /**
     * Tampilkan detail Purchase Order.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'receiver', 'outlet', 'details.ingredient.unitRelation', 'details.unit']);
        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Proses Penerimaan Purchase Order.
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->back()->with('error', 'Purchase Order ini sudah diterima sebelumnya!');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->update([
                'status' => 'received',
                'received_by' => Auth::id(),
                'received_at' => now(),
            ]);

            foreach ($purchaseOrder->details as $detail) {
                $qtyToAdd = $detail->quantity;

                // Konversi satuan jika satuan beli berbeda dengan satuan dasar stok
                if ($detail->unit_id && $detail->ingredient->unit_id && $detail->unit_id != $detail->ingredient->unit_id) {
                    $qtyToAdd = UnitConversion::convert($detail->quantity, $detail->unit_id, $detail->ingredient->unit_id);
                }

                // Tambahkan stok pada outlet penerimaan
                $detail->ingredient->adjustStock(
                    $purchaseOrder->outlet_id,
                    'in',
                    $qtyToAdd,
                    'Penerimaan PO: ' . $purchaseOrder->po_number
                );
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder->id)->with('success', 'Barang PO berhasil diterima dan stok telah ditambahkan ke outlet!');
    }
}
