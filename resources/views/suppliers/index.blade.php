@extends('layouts.app')

@section('title', 'Supplier - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Supplier</h1>
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addSupplierModal">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Supplier
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

    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 bg-white border-0">
            <h6 class="m-0 font-weight-bold text-dark">Daftar Supplier</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Supplier</th>
                            <th>Telepon</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td class="font-weight-bold text-gray-900">{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone ?? '-' }}</td>
                                <td>{{ $supplier->email ?? '-' }}</td>
                                <td>{{ $supplier->address ?? '-' }}</td>
                                @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Tombol Edit -->
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editSupplierModal"
                                                data-id="{{ $supplier->id }}" data-name="{{ $supplier->name }}" data-phone="{{ $supplier->phone }}" data-email="{{ $supplier->email }}" data-address="{{ $supplier->address }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            @if(Auth::user()->isAdmin())
                                                <!-- Tombol Hapus -->
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="if(confirm('Apakah Anda yakin ingin menghapus supplier ini?')) { document.getElementById('delete-supplier-{{ $supplier->id }}').submit(); }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>

                                        @if(Auth::user()->isAdmin())
                                            <form id="delete-supplier-{{ $supplier->id }}" action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada data supplier.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <!-- Modal Tambah Supplier -->
        <div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Tambah Supplier Baru</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Supplier <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="cth: PT Maju Jaya" required>
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="phone" class="form-control" placeholder="cth: 08123456789">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" placeholder="cth: supplier@mail.com">
                            </div>
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Alamat supplier..."></textarea>
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

        <!-- Modal Edit Supplier -->
        <div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Supplier</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Supplier <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="phone" id="edit_phone" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="address" id="edit_address" class="form-control" rows="3"></textarea>
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
    $('#editSupplierModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var phone = button.data('phone');
        var email = button.data('email');
        var address = button.data('address');

        var modal = $(this);
        modal.find('#edit_name').val(name);
        modal.find('#edit_phone').val(phone);
        modal.find('#edit_email').val(email);
        modal.find('#edit_address').val(address);
        modal.find('#editForm').attr('action', '/suppliers/' + id);
    });
</script>
@endpush
