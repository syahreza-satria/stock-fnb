@extends('layouts.app')

@section('title', 'Beranda - Inventaris F&B')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Beranda Inventaris F&B</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Total Bahan Baku -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Bahan Baku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalIngredients }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Resep -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Resep</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRecipes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Peringatan Stok Menipis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan stok menipis jika ada -->
    @if($lowStockCount > 0)
        <div class="alert alert-warning border-left-warning shadow-sm" role="alert">
            <h5 class="alert-heading font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Perhatian: Terdeteksi Bahan Baku dengan Stok Menipis!</h5>
            <p class="mb-0">Beberapa bahan baku sudah berada di bawah batas minimum. Segera lakukan restok.</p>
        </div>
    @endif

    <div class="row">
        <!-- Tabel Bahan Baku Stok Menipis -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Bahan Baku Stok Menipis</h6>
                </div>
                <div class="card-body">
                    @if($lowStockIngredients->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="mb-0">Semua bahan baku memiliki stok yang cukup!</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Stok Saat Ini</th>
                                        <th>Batas Minimum</th>
                                        <th>Satuan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockIngredients as $ingredient)
                                        <tr>
                                            <td class="font-weight-bold">{{ $ingredient->name }}</td>
                                            <td class="text-danger font-weight-bold">{{ number_format($ingredient->stock, 2) }}</td>
                                            <td>{{ number_format($ingredient->minimum_stock, 2) }}</td>
                                            <td><span class="badge badge-secondary">{{ $ingredient->unit }}</span></td>
                                            <td>
                                                @if($ingredient->stock == 0)
                                                    <span class="badge badge-danger">STOK HABIS</span>
                                                @else
                                                    <span class="badge badge-warning">STOK MENIPIS</span>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hak Akses Role Anda</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-2"></i>
                        <h5>{{ ucfirst(Auth::user()->role) }}</h5>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Lihat Laporan
                            <span class="badge badge-success"><i class="fas fa-check"></i> Diizinkan</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Kelola Stok
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                <span class="badge badge-success"><i class="fas fa-check"></i> Diizinkan</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Dibatasi</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Kelola Resep
                            @if(Auth::user()->isAdmin())
                                <span class="badge badge-success"><i class="fas fa-check"></i> Diizinkan</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Dibatasi (Admin)</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
