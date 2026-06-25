@extends('layouts.app')

@section('title', 'Buat Purchase Order Baru - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Purchase Order Baru</h1>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar PO
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
        @csrf
        <div class="row">
            <!-- Left Panel: Supplier & General Details -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3 bg-white border-0">
                        <h6 class="m-0 font-weight-bold text-dark">Informasi Purchase Order</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-control" required>
                                <option value="" disabled selected>Pilih supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="outlet_id">Tujuan Outlet/Gudang <span class="text-danger">*</span></label>
                            <select name="outlet_id" id="outlet_id" class="form-control" required>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ $outlet->is_default ? 'selected' : '' }}>{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="mt-4">
                            <h5 class="text-gray-800 font-weight-bold">Ringkasan Biaya</h5>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="text-gray-600">Total Nilai PO:</span>
                                <span class="h4 mb-0 font-weight-bold text-primary" id="total_display">Rp 0,00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Items Ordered -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3 bg-white border-0 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-dark">Item Bahan Baku yang Dipesan</h6>
                        <button type="button" class="btn btn-sm btn-success" id="add-row-btn">
                            <i class="fas fa-plus mr-1"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Bahan Baku <span class="text-danger">*</span></th>
                                        <th style="width: 20%;">Satuan Beli <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Jumlah <span class="text-danger">*</span></th>
                                        <th style="width: 20%;">Harga Satuan (Rp) <span class="text-danger">*</span></th>
                                        <th style="width: 10%;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                    <!-- Dynamic rows will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-save mr-1"></i> Simpan & Kirim PO
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var rowIndex = 0;
        
        // Add Initial Row
        addRow();

        $('#add-row-btn').click(function() {
            addRow();
        });

        $(document).on('click', '.remove-row-btn', function() {
            if ($('#items-tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotal();
            } else {
                alert('Minimal harus ada 1 item di dalam Purchase Order!');
            }
        });

        $(document).on('change', '.ingredient-select', function() {
            var selectedOption = $(this).find('option:selected');
            var baseUnitId = selectedOption.data('unitid');
            // Auto select matching unit in dropdown if present
            if(baseUnitId) {
                $(this).closest('tr').find('.unit-select').val(baseUnitId);
            }
        });

        $(document).on('input', '.qty-input, .price-input', function() {
            calculateTotal();
        });

        function addRow() {
            var html = `
                <tr data-row="${rowIndex}">
                    <td>
                        <select name="items[${rowIndex}][ingredient_id]" class="form-control ingredient-select" required>
                            <option value="" disabled selected>Pilih bahan baku...</option>
                            @foreach($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}" data-unitid="{{ $ingredient->unit_id }}">{{ $ingredient->name }} (dasar: {{ $ingredient->unitRelation->abbreviation ?? $ingredient->unit }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="items[${rowIndex}][unit_id]" class="form-control unit-select" required>
                            <option value="" disabled selected>Pilih satuan...</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }} (${unit->abbreviation})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][quantity]" class="form-control qty-input" min="0.01" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${rowIndex}][unit_price]" class="form-control price-input" min="0.00" placeholder="0.00" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#items-tbody').append(html);
            rowIndex++;
            calculateTotal();
        }

        function calculateTotal() {
            var total = 0;
            $('#items-tbody tr').each(function() {
                var qty = parseFloat($(this).find('.qty-input').val()) || 0;
                var price = parseFloat($(this).find('.price-input').val()) || 0;
                total += qty * price;
            });

            var formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(total);

            $('#total_display').text(formatted);
        }
    });
</script>
@endpush
