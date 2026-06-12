<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\Ingredient;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan dengan filter.
     */
    public function index(Request $request)
    {
        $ingredients = Ingredient::orderBy('name')->get();

        $query = StockMovement::with('ingredient');

        if ($request->filled('ingredient_id')) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();

        // Buat daftar bulan dan tahun yang tersedia untuk pilihan export
        $availableMonths = StockMovement::selectRaw('YEAR(created_at) as tahun, MONTH(created_at) as bulan')
            ->groupBy('tahun', 'bulan')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        return view('reports.index', compact('movements', 'ingredients', 'availableMonths'));
    }

    /**
     * Export laporan bulanan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
            'tahun' => ['nullable', 'integer', 'min:2000'],
        ]);

        $query = StockMovement::with('ingredient');

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $ingredient_name = null;

        // Filter berdasarkan bulan & tahun
        if ($bulan && $tahun) {
            $query->whereYear('created_at', $tahun)
                  ->whereMonth('created_at', $bulan);
        }

        // Filter berdasarkan bahan baku (opsional)
        if ($request->filled('ingredient_id')) {
            $query->where('ingredient_id', $request->ingredient_id);
            $ingredient = Ingredient::find($request->ingredient_id);
            $ingredient_name = $ingredient ? $ingredient->name : null;
        }

        $movements = $query->orderBy('created_at', 'asc')->get();

        // Buat nama file PDF
        if ($bulan && $tahun) {
            $periodLabel = Carbon::create($tahun, $bulan)->locale('id')->translatedFormat('F_Y');
        } else {
            $periodLabel = 'Semua_Periode';
        }
        $filename = "Laporan_Stok_{$periodLabel}.pdf";

        $pdf = Pdf::loadView('reports.pdf', compact('movements', 'bulan', 'tahun', 'ingredient_name'))
                  ->setPaper('a4', 'landscape')
                  ->setOptions([
                      'defaultFont'    => 'DejaVu Sans',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => false,
                      'dpi'            => 150,
                  ]);

        return $pdf->download($filename);
    }
}
