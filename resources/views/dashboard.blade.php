@extends('layouts.app')

@section('title', 'Beranda - Inventaris F&B')

@push('styles')
<style>
    .dashboard-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none !important;
        border-radius: 16px !important;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08) !important;
    }
    .metric-icon-box {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }
    .badge-premium {
        border-radius: 30px;
        padding: 5px 12px;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-primary-premium {
        background-color: rgba(78, 115, 223, 0.1);
        color: #4e73df;
    }
    .badge-success-premium {
        background-color: rgba(28, 200, 138, 0.1);
        color: #1cc88a;
    }
    .badge-warning-premium {
        background-color: rgba(246, 194, 62, 0.1);
        color: #f6c23e;
    }
    .badge-danger-premium {
        background-color: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
    }
    .custom-table th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.8px;
        color: #5a5c69;
        border-bottom: 2px solid #e3e6f0 !important;
    }
    .custom-table td {
        vertical-align: middle !important;
        color: #4e5154;
    }
</style>
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold" style="letter-spacing: -0.5px;">Dashboard Analitik</h1>
            <p class="text-muted mb-0 small">Ringkasan real-time operasional & inventaris bahan baku</p>
        </div>
        <div class="bg-white px-3 py-2 rounded-pill shadow-sm border text-sm text-gray-700 font-weight-bold">
            <i class="fas fa-calendar-alt mr-2 text-primary"></i>{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Content Row (Metrics Cards) -->
    <div class="row">
        <!-- Total Bahan Baku -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100 py-3 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-2" style="letter-spacing: 1px;">
                                Total Bahan Baku</div>
                            <div class="h2 mb-1 font-weight-bold text-gray-900">{{ $totalIngredients }}</div>
                            <span class="badge badge-primary-premium badge-premium">Bahan Terdaftar</span>
                        </div>
                        <div class="col-auto">
                            <div class="metric-icon-box" style="background-color: rgba(78, 115, 223, 0.12);">
                                <i class="fas fa-leaf fa-lg text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Resep -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100 py-3 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-2" style="letter-spacing: 1px;">
                                Total Resep</div>
                            <div class="h2 mb-1 font-weight-bold text-gray-900">{{ $totalRecipes }}</div>
                            <span class="badge badge-success-premium badge-premium">Menu Resep</span>
                        </div>
                        <div class="col-auto">
                            <div class="metric-icon-box" style="background-color: rgba(28, 200, 138, 0.12);">
                                <i class="fas fa-book-open fa-lg text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card shadow-sm h-100 py-3 bg-white">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-2" style="letter-spacing: 1px;">
                                Peringatan Stok</div>
                            <div class="h2 mb-1 font-weight-bold text-gray-900">{{ $lowStockCount }}</div>
                            @if($lowStockCount > 0)
                                <span class="badge badge-danger-premium badge-premium">Butuh Perhatian</span>
                            @else
                                <span class="badge badge-success-premium badge-premium">Stok Aman</span>
                            @endif
                        </div>
                        <div class="col-auto">
                            <div class="metric-icon-box" style="background-color: rgba(246, 194, 62, 0.12);">
                                <i class="fas fa-exclamation-triangle fa-lg text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Warning -->
    @if($lowStockCount > 0)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center py-3 mb-4" role="alert" style="background-color: #fffaf0; border-left: 4px solid #f6c23e !important; border-radius: 12px;">
            <i class="fas fa-exclamation-circle text-warning mr-3 fa-2x"></i>
            <div>
                <h6 class="font-weight-bold mb-1 text-warning-dark" style="color: #c48a04;">Perhatian: Stok Menipis!</h6>
                <p class="mb-0 text-xs text-gray-700">Terdapat <strong>{{ $lowStockCount }}</strong> bahan baku yang berada di bawah atau sama dengan batas minimum stok aman. Segera lakukan restok / order PO.</p>
            </div>
        </div>
    @endif

    <!-- Charts Row -->
    <div class="row font-weight-bold">
        <!-- Bar Chart: Stock Levels -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
                <div class="card-header py-3 bg-white border-0 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fas fa-chart-bar mr-2 text-primary"></i> Top 10 Stok Bahan Baku Terbanyak
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar" style="position: relative; height: 320px;">
                        <canvas id="stockBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doughnut Chart: Movements Ratio -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
                <div class="card-header py-3 bg-white border-0 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fas fa-chart-pie mr-2 text-success"></i> Rasio Aktivitas Stok
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie" style="position: relative; height: 320px;">
                        <canvas id="movementDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Row -->
    <div class="row">
        <!-- Tabel Bahan Baku Stok Menipis -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 16px;">
                <div class="card-header py-3 bg-white border-0 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-danger"></i> Daftar Bahan Baku Stok Menipis
                    </h6>
                </div>
                <div class="card-body">
                    @if($lowStockIngredients->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <div class="d-inline-flex p-4 rounded-circle mb-3" style="background-color: rgba(28, 200, 138, 0.08);">
                                <i class="fas fa-check-circle text-success fa-3x"></i>
                            </div>
                            <p class="mb-0 font-weight-bold text-gray-800">Semua bahan baku aman!</p>
                            <small class="text-xs">Stok di atas batas minimum peringatan.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover border-0 align-middle custom-table mb-0">
                                <thead class="bg-light text-gray-700 text-xs">
                                    <tr>
                                        <th class="border-0">Bahan Baku</th>
                                        <th class="border-0 text-right">Stok Saat Ini</th>
                                        <th class="border-0 text-right">Batas Minimum</th>
                                        <th class="border-0 text-center">Satuan</th>
                                        <th class="border-0 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm">
                                    @foreach($lowStockIngredients as $ingredient)
                                        <tr>
                                            <td class="font-weight-bold text-gray-900 align-middle">{{ $ingredient->name }}</td>
                                            <td class="text-danger font-weight-bold text-right align-middle">{{ number_format($ingredient->stock, 2) }}</td>
                                            <td class="text-gray-600 text-right align-middle">{{ number_format($ingredient->minimum_stock, 2) }}</td>
                                            <td class="text-center align-middle">
                                                <span class="badge badge-primary-premium badge-premium" style="font-size: 10px; font-weight: bold;">
                                                    {{ $ingredient->unitRelation->abbreviation ?? $ingredient->unit }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($ingredient->stock == 0)
                                                    <span class="badge badge-danger-premium badge-premium">HABIS</span>
                                                @else
                                                    <span class="badge badge-warning-premium badge-premium">MENIPIS</span>
                                                @endif
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

        <!-- Hak akses berdasarkan role -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100 bg-white" style="border-radius: 16px;">
                <div class="card-header py-3 bg-white border-0 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fas fa-user-shield mr-2 text-primary"></i> Hak Akses Role Anda
                    </h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="text-center py-3">
                        <div class="d-inline-flex p-3 rounded-circle shadow-sm mb-3" style="background-color: rgba(78, 115, 223, 0.08);">
                            <i class="fas fa-shield-alt fa-2x text-primary"></i>
                        </div>
                        <h4 class="font-weight-bold text-gray-900 mb-1" style="letter-spacing: -0.5px;">{{ ucfirst(Auth::user()->role) }}</h4>
                        <span class="text-xs text-muted">Izin sistem aktif</span>
                    </div>
                    <ul class="list-group list-group-flush rounded shadow-sm border-0 bg-light p-2">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0 bg-transparent">
                            <span class="text-sm font-weight-bold text-gray-800">Lihat Laporan</span>
                            <span class="badge badge-success-premium badge-premium"><i class="fas fa-check mr-1"></i> Aktif</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0 bg-transparent">
                            <span class="text-sm font-weight-bold text-gray-800">Kelola Stok</span>
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                <span class="badge badge-success-premium badge-premium"><i class="fas fa-check mr-1"></i> Aktif</span>
                            @else
                                <span class="badge badge-danger-premium badge-premium"><i class="fas fa-times mr-1"></i> Terbatas</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0 bg-transparent">
                            <span class="text-sm font-weight-bold text-gray-800">Kelola Resep</span>
                            @if(Auth::user()->isAdmin())
                                <span class="badge badge-success-premium badge-premium"><i class="fas fa-check mr-1"></i> Aktif</span>
                            @else
                                <span class="badge badge-danger-premium badge-premium"><i class="fas fa-times mr-1"></i> Terbatas</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Bar Chart: Stock Levels
        var ctxBar = document.getElementById('stockBarChart').getContext('2d');
        
        // Gradient color for Bar Chart
        var primaryGradient = ctxBar.createLinearGradient(0, 0, 0, 300);
        primaryGradient.addColorStop(0, 'rgba(78, 115, 223, 0.95)');
        primaryGradient.addColorStop(1, 'rgba(78, 115, 223, 0.2)');

        var stockBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartIngredients->pluck('name')) !!},
                datasets: [{
                    label: 'Stok Saat Ini',
                    backgroundColor: primaryGradient,
                    hoverBackgroundColor: '#2e59d9',
                    borderColor: '#4e73df',
                    borderWidth: 1.5,
                    borderRadius: 8,
                    borderSkipped: false,
                    data: {!! json_encode($chartIngredients->pluck('stock')) !!},
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(234, 236, 244, 0.6)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#858796',
                            font: {
                                family: 'Inter',
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#858796',
                            font: {
                                family: 'Inter',
                                size: 11,
                                weight: 500
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });

        // 2. Doughnut Chart: Movement Ratio
        var ctxPie = document.getElementById('movementDoughnutChart').getContext('2d');
        var movementDoughnutChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Stok Masuk', 'Stok Keluar'],
                datasets: [{
                    data: [{{ $movementInCount }}, {{ $movementOutCount }}],
                    backgroundColor: ['#1cc88a', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#be2617'],
                    hoverBorderColor: "#ffffff",
                    borderWidth: 3
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20,
                            font: {
                                family: 'Inter',
                                size: 12,
                                weight: 600
                            },
                            color: '#4e5154'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                cutout: '72%'
            }
        });
    });
</script>
@endpush
