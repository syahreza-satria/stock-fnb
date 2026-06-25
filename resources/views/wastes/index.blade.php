@extends('layouts.app')

@section('title', 'Pembuangan (Waste) - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pembuangan Bahan Baku (Waste)</h1>
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <button class="btn btn-danger shadow-sm" data-toggle="modal" data-target="#addWasteModal">
                <i class="fas fa-trash-alt fa-sm text-white-50 mr-1"></i> Catat Pembuangan
            </button>
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
            <h6 class="m-0 font-weight-bold text-dark">Log Pembuangan Bahan Baku (Waste)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Waktu Pencatatan</th>
                            <th>Nama Bahan Baku</th>
                            <th>Jumlah Terbuang</th>
                            <th>Outlet / Gudang</th>
                            <th>Alasan</th>
                            <th>Keterangan Tambahan</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wastes as $waste)
                            <tr>
                                <td>{{ $waste->created_at->locale('id')->translatedFormat('d F Y, H:i') }}</td>
                                <td class="font-weight-bold text-gray-900">{{ $waste->ingredient->name }}</td>
                                <td class="text-danger font-weight-bold">
                                    -{{ number_format($waste->quantity, 2) }} <span class="badge badge-secondary">{{ $waste->ingredient->unitRelation->abbreviation ?? $waste->ingredient->unit }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-warehouse mr-1 text-gray-500"></i> {{ $waste->outlet->name ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge @if($waste->reason === 'Kadaluarsa') badge-warning @elseif($waste->reason === 'Tumpah') badge-info @elseif($waste->reason === 'Rusak / Basi') badge-danger @else badge-secondary @endif">
                                        {{ $waste->reason }}
                                    </span>
                                </td>
                                <td>{{ $waste->description ?? '-' }}</td>
                                <td>{{ $waste->user->name }} <span class="badge badge-light">({{ ucfirst($waste->user->role) }})</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada catatan pembuangan bahan baku.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <!-- Modal Catat Pembuangan -->
        <div class="modal fade" id="addWasteModal" tabindex="-1" role="dialog" aria-labelledby="addWasteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content font-weight-bold">
                    <form action="{{ route('wastes.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addWasteModalLabel">Form Catat Pembuangan Bahan Baku</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body font-weight-bold">
                            <div class="form-group">
                                <label for="outlet_id">Outlet / Gudang <span class="text-danger">*</span></label>
                                <select name="outlet_id" id="outlet_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih lokasi...</option>
                                    @foreach($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="ingredient_id">Bahan Baku <span class="text-danger">*</span></label>
                                <select name="ingredient_id" id="ingredient_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih bahan baku...</option>
                                    @foreach($ingredients as $ingredient)
                                        <option value="{{ $ingredient->id }}" data-unit="{{ $ingredient->unitRelation->abbreviation ?? $ingredient->unit }}">
                                            {{ $ingredient->name }} (Total Stok: {{ number_format($ingredient->stock, 2) }} {{ $ingredient->unitRelation->abbreviation ?? $ingredient->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="quantity">Jumlah Terbuang <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="quantity" id="quantity" class="form-control" placeholder="0.00" min="0.01" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="unit-label">-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reason">Alasan Pembuangan <span class="text-danger">*</span></label>
                                <select name="reason" id="reason" class="form-control" required>
                                    <option value="Rusak / Basi">Rusak / Basi</option>
                                    <option value="Kadaluarsa">Kadaluarsa</option>
                                    <option value="Tumpah">Tumpah / Kebocoran</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="description">Keterangan / Catatan Tambahan</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Masukkan detail tambahan (opsional)"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-danger" type="submit" id="submit-btn">Simpan Pembuangan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#ingredient_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var unit = selectedOption.data('unit');
            $('#unit-label').text(unit);
        });
    });
</script>
@endpush
