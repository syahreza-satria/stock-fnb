@extends('layouts.app')

@section('title', 'Purchase Order - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Purchase Orders (PO)</h1>
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Buat PO Baru
            </a>
        @endif
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

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-dark">Daftar Purchase Order</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nomor PO</th>
                            <th>Supplier</th>
                            <th>Tanggal Pembuatan</th>
                            <th>Total Nominal</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr>
                                <td>
                                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="font-weight-bold">
                                        {{ $po->po_number }}
                                    </a>
                                </td>
                                <td>{{ $po->supplier->name }}</td>
                                <td>{{ $po->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</td>
                                <td class="font-weight-bold">Rp {{ number_format($po->total_amount, 2, ',', '.') }}</td>
                                <td>
                                    @if($po->status === 'pending')
                                        <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> PENDING</span>
                                    @elseif($po->status === 'received')
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> DITERIMA</span>
                                    @else
                                        <span class="badge badge-secondary">{{ strtoupper($po->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $po->user->name }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye mr-1"></i> Detail
                                        </a>
                                        @if($po->status === 'pending' && (Auth::user()->isAdmin() || Auth::user()->isStaff()))
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="if(confirm('Apakah Anda yakin ingin memproses penerimaan barang untuk PO ini?')) { document.getElementById('receive-po-{{ $po->id }}').submit(); }">
                                                <i class="fas fa-clipboard-check mr-1"></i> Terima Barang
                                            </button>
                                            <form id="receive-po-{{ $po->id }}" action="{{ route('purchase-orders.receive', $po->id) }}" method="POST" style="display:none;">
                                                @csrf
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada data Purchase Order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
