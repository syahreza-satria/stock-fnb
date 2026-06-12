@extends('layouts.app')

@section('title', 'Simulasi Penjualan - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">POS / Simulasi Penjualan</h1>
    </div>

    <div class="alert alert-info border-left-info shadow-sm" role="alert">
        <h5 class="alert-heading font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Simulasi Pengurangan Stok dari Penjualan</h5>
        <p class="mb-0">Pilih resep menu di bawah, masukkan jumlah pesanan, lalu proses penjualan. Sistem akan memvalidasi ketersediaan stok bahan baku terlebih dahulu, kemudian secara otomatis mengurangi stok dan mencatat transaksi ke dalam log.</p>
    </div>

    <div class="row">
        @foreach($recipes as $recipe)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-gray-800">{{ $recipe->name }}</h6>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="mb-4">
                            <span class="text-xs font-weight-bold text-uppercase text-muted">Bahan baku yang dibutuhkan per porsi:</span>
                            <ul class="list-group list-group-flush small mt-2">
                                @foreach($recipe->ingredients as $ing)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-0">
                                        {{ $ing->name }}
                                        <span class="text-muted">{{ number_format($ing->pivot->quantity, 2) }} {{ $ing->unit }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                <form action="{{ route('orders.sell') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-shopping-cart text-primary"></i></span>
                                        </div>
                                        <input type="number" name="quantity" class="form-control" value="1" min="1" placeholder="Jumlah" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-check mr-1"></i>Proses Penjualan
                                    </button>
                                </form>
                            @else
                                <div class="text-center py-2 text-danger small font-weight-bold">
                                    <i class="fas fa-lock mr-1"></i>Role Owner tidak diizinkan memproses penjualan.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
