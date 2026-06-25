@extends('layouts.app')

@section('title', 'Bahan Baku - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventaris Bahan Baku</h1>
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addIngredientModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Bahan Baku
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
            <h6 class="m-0 font-weight-bold text-dark">Daftar Bahan Baku</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Total Stok</th>
                            <th>Stok per Outlet/Gudang</th>
                            <th>Stok Minimum</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ingredients as $ingredient)
                            <tr>
                                <td class="font-weight-bold text-gray-900">{{ $ingredient->name }}</td>
                                <td class="{{ $ingredient->stock <= $ingredient->minimum_stock ? 'text-danger font-weight-bold' : '' }}">
                                    {{ number_format($ingredient->stock, 2) }}
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0 text-xs">
                                        @forelse($ingredient->outlets as $out)
                                            <li>
                                                <i class="fas fa-warehouse mr-1 text-gray-500"></i>
                                                {{ $out->name }}: <span class="text-primary font-weight-bold">{{ number_format($out->pivot->stock, 2) }}</span>
                                            </li>
                                        @empty
                                            <li class="text-muted">Stok kosong di semua outlet</li>
                                        @endforelse
                                    </ul>
                                </td>
                                <td>{{ number_format($ingredient->minimum_stock, 2) }}</td>
                                <td><span class="badge badge-secondary">{{ $ingredient->unitRelation->abbreviation ?? $ingredient->unit }}</span></td>
                                <td>
                                    @if($ingredient->stock == 0)
                                        <span class="badge badge-danger">STOK HABIS</span>
                                    @elseif($ingredient->stock <= $ingredient->minimum_stock)
                                        <span class="badge badge-warning">STOK MENIPIS</span>
                                    @else
                                        <span class="badge badge-success">STOK AMAN</span>
                                    @endif
                                </td>
                                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Tombol Sesuaikan Stok -->
                                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#adjustStockModal"
                                                data-id="{{ $ingredient->id }}" data-name="{{ $ingredient->name }}" data-unit="{{ $ingredient->unitRelation->abbreviation ?? $ingredient->unit }}">
                                                <i class="fas fa-arrows-alt-v mr-1"></i> Stok Masuk/Keluar
                                            </button>

                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editIngredientModal"
                                                data-id="{{ $ingredient->id }}" data-name="{{ $ingredient->name }}" data-unitid="{{ $ingredient->unit_id }}" data-min="{{ $ingredient->minimum_stock }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            @if(Auth::user()->isAdmin())
                                                <!-- Tombol Hapus -->
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="if(confirm('Apakah Anda yakin ingin menghapus bahan baku ini?')) { document.getElementById('delete-ingredient-{{ $ingredient->id }}').submit(); }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>

                                        @if(Auth::user()->isAdmin())
                                            <form id="delete-ingredient-{{ $ingredient->id }}" action="{{ route('ingredients.destroy', $ingredient->id) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Bahan Baku -->
    <div class="modal fade" id="addIngredientModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content font-weight-bold">
                <form action="{{ route('ingredients.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Tambah Bahan Baku Baru</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Bahan Baku</label>
                            <input type="text" name="name" class="form-control" placeholder="cth: Susu Segar, Biji Espresso" required>
                        </div>
                        <div class="form-group">
                            <label>Satuan Pengukuran</label>
                            <select name="unit_id" class="form-control" required>
                                <option value="" disabled selected>Pilih satuan dasar...</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stok Awal (Akan dimasukkan ke Outlet Utama)</label>
                            <input type="number" step="0.01" name="stock" class="form-control" value="0.00" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Batas Minimum Stok (Peringatan)</label>
                            <input type="number" step="0.01" name="minimum_stock" class="form-control" value="10.00" min="0" required>
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

    <!-- Modal Edit Bahan Baku -->
    <div class="modal fade" id="editIngredientModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content font-weight-bold">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Bahan Baku</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Bahan Baku</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Satuan Pengukuran</label>
                            <select name="unit_id" id="edit_unit_id" class="form-control" required>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Batas Minimum Stok (Peringatan)</label>
                            <input type="number" step="0.01" name="minimum_stock" id="edit_min_stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Penyesuaian Stok -->
    <div class="modal fade" id="adjustStockModal" tabindex="-1" role="dialog" aria-labelledby="adjustModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content font-weight-bold">
                <form id="adjustForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="adjustModalLabel">Penyesuaian Stok untuk <span id="adjust_title" class="font-weight-bold text-primary"></span></h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="outlet_id">Outlet / Gudang <span class="text-danger">*</span></label>
                            <select name="outlet_id" id="outlet_id" class="form-control" required>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jenis Penyesuaian</label>
                            <select name="type" class="form-control" required>
                                <option value="in">Stok Masuk (Restok / Pembelian)</option>
                                <option value="out">Stok Keluar (Pemakaian Manual / Terbuang)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Jumlah (<span id="adjust_unit_label"></span>)</label>
                            <input type="number" step="0.01" name="quantity" class="form-control" placeholder="0.00" min="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan / Catatan</label>
                            <input type="text" name="description" class="form-control" placeholder="cth: Restok, Koreksi stok, dll.">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Proses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $('#editIngredientModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var unitId = button.data('unitid');
        var min = button.data('min');

        var modal = $(this);
        modal.find('#edit_name').val(name);
        modal.find('#edit_unit_id').val(unitId);
        modal.find('#edit_min_stock').val(min);
        modal.find('#editForm').attr('action', '/ingredients/' + id);
    });

    $('#adjustStockModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var unit = button.data('unit');

        var modal = $(this);
        modal.find('#adjust_title').text(name);
        modal.find('#adjust_unit_label').text(unit);
        modal.find('#adjustForm').attr('action', '/ingredients/' + id + '/adjust');
    });
</script>
@endpush
