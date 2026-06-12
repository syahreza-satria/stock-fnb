@extends('layouts.app')

@section('title', 'Log Pergerakan Stok - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Log Pergerakan Stok</h1>
    </div>

    <div class="row">
        <!-- Panel Filter Log -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-1"></i>Filter Pergerakan</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Bahan Baku</label>
                                <select name="ingredient_id" class="form-control">
                                    <option value="">-- Semua Bahan Baku --</option>
                                    @foreach($ingredients as $ing)
                                        <option value="{{ $ing->id }}" {{ request('ingredient_id') == $ing->id ? 'selected' : '' }}>
                                            {{ $ing->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Tanggal Mulai</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Tanggal Akhir</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search mr-1"></i>Cari</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Export PDF -->
        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-danger">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-file-pdf mr-1"></i>Export Laporan PDF</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.export.pdf') }}" target="_blank">
                        <div class="form-group">
                            <label class="font-weight-bold small">Bulan & Tahun</label>
                            <select name="bulan" class="form-control form-control-sm" id="bulanSelect">
                                <option value="">-- Semua Periode --</option>
                                @foreach($availableMonths as $period)
                                    <option value="{{ $period->bulan }}"
                                        data-tahun="{{ $period->tahun }}"
                                        {{ (request('bulan') == $period->bulan && request('tahun') == $period->tahun) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create($period->tahun, $period->bulan)->locale('id')->translatedFormat('F Y') }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="tahun" id="tahunInput" value="{{ request('tahun') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small">Bahan Baku (Opsional)</label>
                            <select name="ingredient_id" class="form-control form-control-sm">
                                <option value="">-- Semua Bahan Baku --</option>
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}">{{ $ing->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-download mr-1"></i>Download PDF
                        </button>
                        <small class="text-muted d-block text-center mt-2">PDF akan dibuka di tab baru</small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Log -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Riwayat Log
                <span class="badge badge-secondary ml-2">{{ $movements->count() }} transaksi</span>
            </h6>
            @if(request('ingredient_id') || request('start_date') || request('end_date'))
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-undo mr-1"></i>Reset Filter</a>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Bahan Baku</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('d M Y, H:i') }}</td>
                                <td class="font-weight-bold">{{ $movement->ingredient->name }}</td>
                                <td>
                                    @if($movement->type === 'in')
                                        <span class="badge badge-success font-weight-bold px-2 py-1"><i class="fas fa-arrow-down mr-1"></i>Stok Masuk</span>
                                    @else
                                        <span class="badge badge-danger font-weight-bold px-2 py-1"><i class="fas fa-arrow-up mr-1"></i>Stok Keluar</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold {{ $movement->type === 'in' ? 'text-success' : 'text-danger' }}">
                                    {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                                    <span class="text-secondary small font-weight-normal">{{ $movement->ingredient->unit }}</span>
                                </td>
                                <td>{{ $movement->description }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada data pergerakan stok yang ditemukan untuk filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-isi input tahun tersembunyi saat pilihan bulan berubah
    document.getElementById('bulanSelect').addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        var tahun = selected.getAttribute('data-tahun') || '';
        document.getElementById('tahunInput').value = tahun;
    });

    // Set nilai awal tahun saat halaman load
    (function() {
        var select = document.getElementById('bulanSelect');
        var selected = select.options[select.selectedIndex];
        var tahun = selected.getAttribute('data-tahun') || '';
        document.getElementById('tahunInput').value = tahun;
    })();
</script>
@endpush
