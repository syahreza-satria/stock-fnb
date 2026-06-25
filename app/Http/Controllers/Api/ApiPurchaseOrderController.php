<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiPurchaseOrderController extends Controller
{
    /**
     * Get all purchase orders.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user'])->orderBy('created_at', 'desc')->get();
        return response()->json($purchaseOrders);
    }

    /**
     * Store a new purchase order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $purchaseOrder = DB::transaction(function () use ($request) {
            $poNumber = 'PO-' . date('YmdHis') . '-' . rand(10, 99);
            
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $totalAmount += $item['quantity'] * $item['unit_price'];
            }

            $po = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $request->supplier_id,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'user_id' => Auth::id(),
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderDetail::create([
                    'purchase_order_id' => $po->id,
                    'ingredient_id' => $item['ingredient_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            return $po;
        });

        return response()->json([
            'message' => 'Purchase Order created successfully!',
            'purchase_order' => $purchaseOrder->load(['supplier', 'user', 'details.ingredient'])
        ], 201);
    }

    /**
     * Show a purchase order.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        return response()->json($purchaseOrder->load(['supplier', 'user', 'receiver', 'details.ingredient']));
    }

    /**
     * Receive a purchase order.
     */
    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['message' => 'Purchase Order has already been received!'], 422);
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->update([
                'status' => 'received',
                'received_by' => Auth::id(),
                'received_at' => now(),
            ]);

            foreach ($purchaseOrder->details as $detail) {
                $detail->ingredient->increment('stock', $detail->quantity);

                StockMovement::create([
                    'ingredient_id' => $detail->ingredient_id,
                    'type' => 'in',
                    'quantity' => $detail->quantity,
                    'description' => 'Penerimaan PO: ' . $purchaseOrder->po_number,
                ]);
            }
        });

        return response()->json([
            'message' => 'Purchase Order items successfully received!',
            'purchase_order' => $purchaseOrder->fresh()->load(['supplier', 'user', 'receiver', 'details.ingredient'])
        ]);
    }
}
