@extends('layouts.app')

@section('title', 'Satuan & Konversi - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Satuan & Konversi</h1>
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
        <!-- Left Panel: Units List & Creation -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white border-0 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark">Daftar Satuan</h6>
                    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addUnitModal">
                            <i class="fas fa-plus mr-1"></i> Tambah Satuan
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nama Satuan</th>
                                    <th>Singkatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($units as $unit)
                                    <tr>
                                        <td class="text-gray-900">{{ $unit->name }}</td>
                                        <td><span class="badge badge-secondary py-1 px-2">{{ $unit->abbreviation }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4">Belum ada data satuan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Conversion Rules & Creation -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white border-0 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-dark">Aturan Konversi Satuan</h6>
                    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addConversionModal">
                            <i class="fas fa-random mr-1"></i> Tambah Aturan Konversi
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Aturan Konversi</th>
                                    <th>Faktor Pengali</th>
                                    @if(Auth::user()->isAdmin())
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($conversions as $conv)
                                    <tr>
                                        <td class="text-gray-900">
                                            1 {{ $conv->fromUnit->name }} ({{ $conv->fromUnit->abbreviation }}) 
                                            &rarr; 
                                            {{ number_format($conv->factor, 2) }} {{ $conv->toUnit->name }} ({{ $conv->toUnit->abbreviation }})
                                        </td>
                                        <td>{{ number_format($conv->factor, 4) }}</td>
                                        @if(Auth::user()->isAdmin())
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="if(confirm('Hapus aturan konversi ini?')) { document.getElementById('delete-conv-{{ $conv->id }}').submit(); }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-conv-{{ $conv->id }}" action="{{ route('units.conversions.destroy', $conv->id) }}" method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Belum ada aturan konversi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <!-- Modal Tambah Satuan -->
        <div class="modal fade" id="addUnitModal" tabindex="-1" role="dialog" aria-labelledby="addUnitModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content font-weight-bold">
                    <form action="{{ route('units.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUnitModalLabel">Tambah Satuan Baru</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="cth: Kilogram, Pcs, Liter" required>
                            </div>
                            <div class="form-group">
                                <label>Singkatan / Simbol <span class="text-danger">*</span></label>
                                <input type="text" name="abbreviation" class="form-control" placeholder="cth: kg, pcs, l, ml" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Konversi -->
        <div class="modal fade" id="addConversionModal" tabindex="-1" role="dialog" aria-labelledby="addConversionModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content font-weight-bold">
                    <form action="{{ route('units.conversions.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addConversionModalLabel">Tambah Aturan Konversi Satuan</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="from_unit_id">Dari Satuan <span class="text-danger">*</span></label>
                                <select name="from_unit_id" id="from_unit_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih satuan asal...</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="factor">Faktor Pengali <span class="text-danger">*</span></label>
                                <input type="number" step="0.0001" name="factor" id="factor" class="form-control" placeholder="cth: 1000.00 (untuk kg ke gram)" min="0.0001" required>
                                <small class="form-text text-muted">Contoh: Jika 1 kg = 1000 gram, pilih Dari: Kilogram, Pengali: 1000, Ke: Gram.</small>
                            </div>

                            <div class="form-group">
                                <label for="to_unit_id">Ke Satuan Dasar <span class="text-danger">*</span></label>
                                <select name="to_unit_id" id="to_unit_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih satuan tujuan...</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-success" type="submit">Simpan Aturan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
