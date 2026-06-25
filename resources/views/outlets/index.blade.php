@extends('layouts.app')

@section('title', 'Outlet & Gudang - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pengaturan Outlet & Gudang</h1>
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addOutletModal">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Outlet/Gudang
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
            <h6 class="m-0 font-weight-bold text-dark">Daftar Outlet & Gudang Penyimpanan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Outlet/Gudang</th>
                            <th>Telepon</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($outlets as $outlet)
                            <tr>
                                <td class="font-weight-bold text-gray-900">
                                    {{ $outlet->name }}
                                </td>
                                <td>{{ $outlet->phone ?? '-' }}</td>
                                <td>{{ $outlet->address ?? '-' }}</td>
                                <td>
                                    @if($outlet->is_default)
                                        <span class="badge badge-success"><i class="fas fa-star mr-1"></i> DEFAULT</span>
                                    @else
                                        <span class="badge badge-light">OUTLET</span>
                                    @endif
                                </td>
                                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editOutletModal"
                                                data-id="{{ $outlet->id }}" data-name="{{ $outlet->name }}" data-phone="{{ $outlet->phone }}" data-address="{{ $outlet->address }}" data-default="{{ $outlet->is_default ? '1' : '0' }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            @if(Auth::user()->isAdmin() && !$outlet->is_default)
                                                <!-- Tombol Hapus -->
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="if(confirm('Apakah Anda yakin ingin menghapus outlet ini? Semua data stok terkait di outlet ini akan ikut terhapus.')) { document.getElementById('delete-outlet-{{ $outlet->id }}').submit(); }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-outlet-{{ $outlet->id }}" action="{{ route('outlets.destroy', $outlet->id) }}" method="POST" style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada data outlet/gudang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <!-- Modal Tambah Outlet -->
        <div class="modal fade" id="addOutletModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content font-weight-bold">
                    <form action="{{ route('outlets.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Outlet/Gudang Baru</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Outlet/Gudang <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="cth: Gudang Pusat, Outlet Kemang" required>
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" placeholder="cth: 0812345678">
                            </div>
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Alamat outlet/gudang..."></textarea>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" name="is_default" value="1" class="form-check-input" id="is_default_check">
                                <label class="form-check-label" for="is_default_check">Jadikan sebagai Outlet Utama (Default untuk POS & Stok awal)</label>
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

        <!-- Modal Edit Outlet -->
        <div class="modal fade" id="editOutletModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content font-weight-bold">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Outlet/Gudang</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Outlet/Gudang <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="phone" id="edit_phone" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea name="address" id="edit_address" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-group form-check" id="default_checkbox_container">
                                <input type="checkbox" name="is_default" value="1" class="form-check-input" id="edit_is_default_check">
                                <label class="form-check-label" for="edit_is_default_check">Jadikan sebagai Outlet Utama (Default)</label>
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
    @endif
@endsection

@push('scripts')
<script>
    $('#editOutletModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var phone = button.data('phone');
        var address = button.data('address');
        var isDefault = button.data('default');

        var modal = $(this);
        modal.find('#edit_name').val(name);
        modal.find('#edit_phone').val(phone);
        modal.find('#edit_address').val(address);
        
        var defaultCheck = modal.find('#edit_is_default_check');
        if (isDefault == '1') {
            defaultCheck.prop('checked', true);
            // Don't allow unchecking if it's already default
            modal.find('#default_checkbox_container').hide();
        } else {
            defaultCheck.prop('checked', false);
            modal.find('#default_checkbox_container').show();
        }

        modal.find('#editForm').attr('action', '/outlets/' + id);
    });
</script>
@endpush
