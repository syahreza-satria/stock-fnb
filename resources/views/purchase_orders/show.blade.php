@extends('layouts.app')

@section('title', 'Detail Purchase Order - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Purchase Order: <span class="font-weight-bold text-primary">{{ $purchaseOrder->po_number }}</span></h1>
        <div class="d-flex flex-wrap mt-2 mt-sm-0" style="gap: 8px;">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar PO
            </a>
            @if($purchaseOrder->status === 'pending' && (Auth::user()->isAdmin() || Auth::user()->isStaff()))
                <button class="btn btn-success shadow-sm" onclick="if(confirm('Apakah Anda yakin semua barang telah diterima dengan baik?')) { document.getElementById('receive-po-form').submit(); }">
                    <i class="fas fa-clipboard-check fa-sm mr-1"></i> Terima & Masukkan ke Stok
                </button>
                <form id="receive-po-form" action="{{ route('purchase-orders.receive', $purchaseOrder->id) }}" method="POST" style="display:none;">
                    @csrf
                </form>
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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Left Panel: General Info -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-dark">Informasi Umum PO</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="text-xs font-weight-bold text-uppercase text-gray-500 d-block">Status</span>
                        @if($purchaseOrder->status === 'pending')
                            <span class="badge badge-warning py-2 px-3"><i class="fas fa-clock mr-1"></i> PENDING (Menunggu Penerimaan)</span>
                        @elseif($purchaseOrder->status === 'received')
                            <span class="badge badge-success py-2 px-3"><i class="fas fa-check-circle mr-1"></i> DITERIMA (Stok Ditambahkan)</span>
                        @endif
                    </div>
                    <hr>
                    <div class="mb-3">
                        <span class="text-xs font-weight-bold text-uppercase text-gray-500 d-block">Tujuan Penerimaan Outlet/Gudang</span>
                        <span class="text-gray-900 d-block"><i class="fas fa-warehouse mr-1 text-primary"></i> {{ $purchaseOrder->outlet->name ?? '-' }}</span>
                        @if($purchaseOrder->outlet && $purchaseOrder->outlet->address)
                            <span class="text-xs text-gray-500 d-block font-italic">{{ $purchaseOrder->outlet->address }}</span>
                        @endif
                    </div>
                    <hr>
                    <div class="mb-3">
                        <span class="text-xs font-weight-bold text-uppercase text-gray-500 d-block">Supplier</span>
                        <span class="font-weight-bold text-gray-900 d-block h5 mb-0">{{ $purchaseOrder->supplier->name }}</span>
                        @if($purchaseOrder->supplier->phone)
                            <span class="text-sm text-gray-600 d-block"><i class="fas fa-phone mr-1"></i> {{ $purchaseOrder->supplier->phone }}</span>
                        @endif
                    </div>
                    <hr>
                    <div class="mb-3">
                        <span class="text-xs font-weight-bold text-uppercase text-gray-500 d-block">Dibuat Oleh</span>
                        <span class="text-gray-800 d-block">{{ $purchaseOrder->user->name }}</span>
                        <span class="text-xs text-gray-500 d-block">{{ $purchaseOrder->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                    </div>
                    @if($purchaseOrder->status === 'received')
                        <hr>
                        <div class="mb-3">
                            <span class="text-xs font-weight-bold text-uppercase text-gray-500 d-block">Diterima Oleh</span>
                            <span class="text-gray-800 d-block">{{ $purchaseOrder->receiver->name ?? '-' }}</span>
                            <span class="text-xs text-gray-500 d-block">{{ $purchaseOrder->received_at->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Panel: Ordered Items Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white border-0">
                    <h6 class="m-0 font-weight-bold text-dark">Detail Barang / Item Dipesan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive font-weight-bold">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Bahan Baku</th>
                                    <th class="text-right">Kuantitas Pemesanan</th>
                                    <th class="text-right">Estimasi Satuan Dasar</th>
                                    <th class="text-right">Harga Satuan</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->details as $detail)
                                    <tr>
                                        <td class="text-gray-900 font-weight-bold">
                                            {{ $detail->ingredient->name }}
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($detail->quantity, 2) }} 
                                            <span class="badge badge-secondary">{{ $detail->unit->abbreviation ?? $detail->ingredient->unit }}</span>
                                        </td>
                                        <td class="text-right text-xs">
                                            @if($detail->unit_id && $detail->ingredient->unit_id && $detail->unit_id != $detail->ingredient->unit_id)
                                                @php
                                                    $converted = \App\Models\UnitConversion::convert($detail->quantity, $detail->unit_id, $detail->ingredient->unit_id);
                                                @endphp
                                                <span class="text-success font-weight-bold">
                                                    &asymp; {{ number_format($converted, 2) }} {{ $detail->ingredient->unitRelation->abbreviation ?? $detail->ingredient->unit }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-right">Rp {{ number_format($detail->unit_price, 2, ',', '.') }}</td>
                                        <td class="text-right text-gray-900">Rp {{ number_format($detail->quantity * $detail->unit_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right text-gray-900">TOTAL NILAI PO</th>
                                    <th class="text-right text-primary h5 font-weight-bold">Rp {{ number_format($purchaseOrder->total_amount, 2, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
