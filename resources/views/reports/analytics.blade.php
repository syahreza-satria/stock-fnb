@extends('layouts.app')

@section('title', 'Analitik Lanjutan - Inventaris F&B')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold" style="letter-spacing: -0.5px;">Analitik Lanjutan</h1>
            <p class="text-muted mb-0 small">Analisa margin, variance stock opname, dead stock, dan tren konsumsi musiman</p>
        </div>
        <div class="d-flex flex-wrap mt-2 mt-sm-0" style="gap: 8px;">
            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                <button class="btn btn-success shadow-sm" data-toggle="modal" data-target="#pricingModal">
                    <i class="fas fa-tags mr-1"></i> Atur Harga & Modal
                </button>
                <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#stockTakeModal">
                    <i class="fas fa-clipboard-check mr-1"></i> Catat Stock Opname
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active font-weight-bold" id="pills-cost-tab" data-toggle="pill" href="#pills-cost" role="tab" aria-controls="pills-cost" aria-selected="true">
                <i class="fas fa-dollar-sign mr-1"></i> Food Cost & Margin
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link font-weight-bold" id="pills-variance-tab" data-toggle="pill" href="#pills-variance" role="tab" aria-controls="pills-variance" aria-selected="false">
                <i class="fas fa-balance-scale mr-1"></i> Variance Report
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link font-weight-bold" id="pills-dead-tab" data-toggle="pill" href="#pills-dead" role="tab" aria-controls="pills-dead" aria-selected="false">
                <i class="fas fa-archive mr-1"></i> Dead Stock Report
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link font-weight-bold" id="pills-seasonal-tab" data-toggle="pill" href="#pills-seasonal" role="tab" aria-controls="pills-seasonal" aria-selected="false">
                <i class="fas fa-chart-line mr-1"></i> Tren Konsumsi Bahan
            </a>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- 1. FOOD COST & MARGIN -->
        <div class="tab-pane fade show active" id="pills-cost" role="tabpanel" aria-labelledby="pills-cost-tab">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-utensils text-primary mr-2"></i>Analisa Margin Keuntungan & Food Cost</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Menu / Resep</th>
                                    <th class="text-right">Harga Jual</th>
                                    <th class="text-right">Modal Bahan (COGS)</th>
                                    <th class="text-right">Margin Bersih (Rp)</th>
                                    <th class="text-center">Food Cost (%)</th>
                                    <th class="text-center">Margin (%)</th>
                                    <th class="text-center">Status Keuntungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($foodCostAnalysis as $item)
                                    <tr>
                                        <td class="font-weight-bold text-gray-900">{{ $item['recipe']->name }}</td>
                                        <td class="text-right">Rp {{ number_format($item['recipe']->selling_price, 2, ',', '.') }}</td>
                                        <td class="text-right text-gray-600">Rp {{ number_format($item['total_cost'], 2, ',', '.') }}</td>
                                        <td class="text-right font-weight-bold {{ $item['margin'] > 0 ? 'text-success' : 'text-danger' }}">
                                            Rp {{ number_format($item['margin'], 2, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge @if($item['food_cost_percentage'] > 45) badge-danger @elseif($item['food_cost_percentage'] > 35) badge-warning @else badge-success @endif">
                                                {{ number_format($item['food_cost_percentage'], 1) }}%
                                            </span>
                                        </td>
                                        <td class="text-center font-weight-bold">
                                            {{ number_format($item['margin_percentage'], 1) }}%
                                        </td>
                                        <td class="text-center">
                                            @if($item['food_cost_percentage'] == 0)
                                                <span class="badge badge-secondary">Belum Diatur</span>
                                            @elseif($item['food_cost_percentage'] <= 35)
                                                <span class="badge badge-success">SEHAT (PRIMA)</span>
                                            @elseif($item['food_cost_percentage'] <= 45)
                                                <span class="badge badge-warning">MODERAT</span>
                                            @else
                                                <span class="badge badge-danger">BOROS (RESTRUKTURISASI)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. VARIANCE REPORT -->
        <div class="tab-pane fade" id="pills-variance" role="tabpanel" aria-labelledby="pills-variance-tab">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-balance-scale text-primary mr-2"></i>Selisih Stok Teoritis vs Aktual (Stock Opname)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Waktu Opname</th>
                                    <th>Outlet / Gudang</th>
                                    <th>Bahan Baku</th>
                                    <th class="text-right">Stok Sistem (Teoritis)</th>
                                    <th class="text-right">Stok Fisik (Aktual)</th>
                                    <th class="text-right">Selisih (Variance)</th>
                                    <th>Keterangan</th>
                                    <th>Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockTakes as $take)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($take->created_at)->locale('id')->translatedFormat('d M Y, H:i') }}</td>
                                        <td><i class="fas fa-warehouse text-gray-500 mr-1"></i> {{ $take->outlet_name }}</td>
                                        <td class="font-weight-bold text-gray-900">{{ $take->ingredient_name }}</td>
                                        <td class="text-right">{{ number_format($take->theoretical_stock, 2) }} {{ $take->ingredient_unit }}</td>
                                        <td class="text-right">{{ number_format($take->actual_stock, 2) }} {{ $take->ingredient_unit }}</td>
                                        <td class="text-right font-weight-bold {{ $take->variance == 0 ? 'text-success' : ($take->variance > 0 ? 'text-primary' : 'text-danger') }}">
                                            {{ $take->variance > 0 ? '+' : '' }}{{ number_format($take->variance, 2) }} {{ $take->ingredient_unit }}
                                        </td>
                                        <td>{{ $take->notes ?? '-' }}</td>
                                        <td>{{ $take->user_name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat pencatatan stock opname / variance.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. DEAD STOCK REPORT -->
        <div class="tab-pane fade" id="pills-dead" role="tabpanel" aria-labelledby="pills-dead-tab">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-archive text-danger mr-2"></i>Item Lambat Bergerak (Dead Stock) - Tanpa Aktivitas Keluar > 30 Hari</h6>
                </div>
                <div class="card-body">
                    @if($deadStock->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <div class="d-inline-flex p-4 rounded-circle mb-3" style="background-color: rgba(28, 200, 138, 0.08);">
                                <i class="fas fa-check-circle text-success fa-3x"></i>
                            </div>
                            <p class="mb-0 font-weight-bold text-gray-800">Semua bahan baku aktif!</p>
                            <small class="text-xs">Tidak ditemukan dead stock di sistem.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Bahan Baku</th>
                                        <th class="text-right">Stok Mengendap</th>
                                        <th>Satuan</th>
                                        <th>Estimasi Nilai Stok Terbuang</th>
                                        <th>Peringatan Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deadStock as $item)
                                        <tr>
                                            <td class="font-weight-bold text-gray-900">{{ $item->name }}</td>
                                            <td class="text-right font-weight-bold">{{ number_format($item->stock, 2) }}</td>
                                            <td><span class="badge badge-secondary">{{ $item->unitRelation->abbreviation ?? $item->unit }}</span></td>
                                            <td class="text-danger font-weight-bold">Rp {{ number_format($item->stock * $item->cost_price, 2, ',', '.') }}</td>
                                            <td>
                                                <span class="badge badge-danger-premium badge-premium"><i class="fas fa-exclamation-triangle mr-1"></i> DEAD STOCK DETECTED</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4. TREN KONSUMSI MUSIMAN -->
        <div class="tab-pane fade" id="pills-seasonal" role="tabpanel" aria-labelledby="pills-seasonal-tab">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-chart-line text-primary mr-2"></i>Tren Pemakaian Bahan Baku Bulanan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Periode Bulan</th>
                                    <th>Nama Bahan Baku</th>
                                    <th class="text-right">Total Pemakaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyConsumption as $consumption)
                                    <tr>
                                        <td class="font-weight-bold text-gray-900">
                                            {{ \Carbon\Carbon::create($consumption->year, $consumption->month)->locale('id')->translatedFormat('F Y') }}
                                        </td>
                                        <td>{{ $consumption->ingredient_name }}</td>
                                        <td class="text-right font-weight-bold text-primary">
                                            {{ number_format($consumption->total_qty, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">Belum ada data konsumsi / pemakaian bahan yang terekam.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL ATUR PRICING & COST -->
    <div class="modal fade" id="pricingModal" tabindex="-1" role="dialog" aria-labelledby="pricingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content font-weight-bold">
                <form action="{{ route('analytics.pricing.update') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="pricingModalLabel">Pengaturan Harga Jual & Harga Modal Bahan</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="text-primary font-weight-bold mb-3">1. Harga Jual Menu / Resep</h6>
                        @foreach($recipes as $idx => $recipe)
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6">{{ $recipe->name }}</div>
                                <div class="col-md-6">
                                    <input type="hidden" name="recipe_prices[{{ $idx }}][id]" value="{{ $recipe->id }}">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="number" step="0.01" name="recipe_prices[{{ $idx }}][selling_price]" value="{{ $recipe->selling_price }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <hr>

                        <h6 class="text-success font-weight-bold mb-3 mt-4">2. Harga Modal Bahan Baku (per Satuan Dasar)</h6>
                        @foreach($ingredients as $idx => $ing)
                            <div class="row align-items-center mb-3">
                                <div class="col-md-6">{{ $ing->name }} ({{ $ing->unitRelation->abbreviation ?? $ing->unit }})</div>
                                <div class="col-md-6">
                                    <input type="hidden" name="ingredient_costs[{{ $idx }}][id]" value="{{ $ing->id }}">
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                        <input type="number" step="0.01" name="ingredient_costs[{{ $idx }}][cost_price]" value="{{ $ing->cost_price }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL CATAT STOCK TAKE (OPNAME) -->
    <div class="modal fade" id="stockTakeModal" tabindex="-1" role="dialog" aria-labelledby="stockTakeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content font-weight-bold">
                <form action="{{ route('analytics.stock-take.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="stockTakeModalLabel">Catat Hasil Stock Opname (Fisik)</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Outlet / Lokasi</label>
                            <select name="outlet_id" class="form-control" required>
                                <option value="" disabled selected>Pilih outlet...</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bahan Baku</label>
                            <select name="ingredient_id" id="stock_take_ingredient_select" class="form-control" required>
                                <option value="" disabled selected>Pilih bahan baku...</option>
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}" data-unit="{{ $ing->unitRelation->abbreviation ?? $ing->unit }}">
                                        {{ $ing->name }} (Teoritis Global: {{ number_format($ing->stock, 2) }} {{ $ing->unitRelation->abbreviation ?? $ing->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stok Fisik Aktual</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="actual_stock" class="form-control" placeholder="0.00" min="0" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="stock_take_unit_label">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan / Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="cth: Perbedaan timbangan, tumpah tidak tercatat, dll.">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Catat & Sesuaikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('#stock_take_ingredient_select').change(function() {
            var selected = $(this).find('option:selected');
            var unit = selected.data('unit');
            $('#stock_take_unit_label').text(unit);
        });
    });
</script>
@endpush
