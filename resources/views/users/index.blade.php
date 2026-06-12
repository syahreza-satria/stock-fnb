@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Pengguna</h1>
        <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addUserModal">
            <i class="fas fa-user-plus fa-sm text-white-50"></i> Tambah Pengguna
        </button>
    </div>

    <!-- Statistik Singkat -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pengguna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Admin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->where('role', 'admin')->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-shield fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Staff & Owner</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->whereIn('role', ['staff', 'owner'])->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-tag fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Pengguna</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Nama</th>
                            <th>Alamat Email</th>
                            <th width="12%">Role</th>
                            <th width="10%">Terdaftar</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $user)
                            <tr {{ $user->id === auth()->id() ? 'class=table-active' : '' }}>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar mr-2">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $user->name }}</div>
                                            @if($user->id === auth()->id())
                                                <small class="text-muted"><i class="fas fa-star text-warning fa-xs"></i> Akun Anda</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-user-shield mr-1"></i>Admin</span>
                                    @elseif($user->role === 'staff')
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-user-tie mr-1"></i>Staff</span>
                                    @else
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-user mr-1"></i>Owner</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <!-- Tombol Edit -->
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editUserModal"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        @if($user->id !== auth()->id())
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="if(confirm('Apakah Anda yakin ingin menghapus pengguna \'{{ addslashes($user->name) }}\'? Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('delete-user-{{ $user->id }}').submit(); }">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-user-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled title="Tidak dapat menghapus akun sendiri">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================== MODAL TAMBAH PENGGUNA ======================== -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addUserModalLabel"><i class="fas fa-user-plus mr-2"></i>Tambah Pengguna Baru</h5>
                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="cth: Budi Santoso" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="cth: budi@fnb.com" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-control" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin — Akses penuh</option>
                                <option value="staff">Staff — Kelola stok & penjualan</option>
                                <option value="owner">Owner — Lihat laporan saja</option>
                            </select>
                            <small class="form-text text-muted">Tentukan hak akses pengguna dalam sistem.</small>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="font-weight-bold">Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================== MODAL EDIT PENGGUNA ======================== -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editUserForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="editUserModalLabel"><i class="fas fa-user-edit mr-2"></i>Edit Pengguna</h5>
                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-control" required>
                                <option value="admin">Admin — Akses penuh</option>
                                <option value="staff">Staff — Kelola stok & penjualan</option>
                                <option value="owner">Owner — Lihat laporan saja</option>
                            </select>
                        </div>
                        <hr>
                        <p class="small text-muted mb-2"><i class="fas fa-info-circle mr-1"></i>Kosongkan kolom kata sandi jika tidak ingin mengubahnya.</p>
                        <div class="form-group">
                            <label class="font-weight-bold">Kata Sandi Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-warning text-white" type="submit"><i class="fas fa-save mr-1"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df, #224abe);
        color: white;
        font-weight: 700;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    $('#editUserModal').on('show.bs.modal', function (event) {
        var btn  = $(event.relatedTarget);
        var id    = btn.data('id');
        var name  = btn.data('name');
        var email = btn.data('email');
        var role  = btn.data('role');

        var modal = $(this);
        modal.find('#edit_name').val(name);
        modal.find('#edit_email').val(email);
        modal.find('#edit_role').val(role);
        modal.find('#editUserForm').attr('action', '/users/' + id);

        // Reset password fields setiap modal dibuka
        modal.find('input[name="password"]').val('');
        modal.find('input[name="password_confirmation"]').val('');
    });
</script>
@endpush
