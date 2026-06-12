@extends('layouts.app')

@section('title', 'Resep - Inventaris F&B')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Resep</h1>
        @if(Auth::user()->isAdmin())
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#addRecipeModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Buat Resep Baru
            </button>
        @endif
    </div>

    <div class="row">
        @foreach($recipes as $recipe)
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ $recipe->name }}</h6>
                        @if(Auth::user()->isAdmin())
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="recipeMenu{{ $recipe->id }}"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                    aria-labelledby="recipeMenu{{ $recipe->id }}">
                                    <button class="dropdown-item edit-recipe-btn" data-toggle="modal" data-target="#editRecipeModal"
                                        data-id="{{ $recipe->id }}" data-name="{{ $recipe->name }}"
                                        data-ingredients="{{ json_encode($recipe->ingredients) }}">
                                        <i class="fas fa-edit mr-2 text-warning"></i>Edit Resep
                                    </button>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus resep ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash mr-2"></i>Hapus Resep
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="font-weight-bold text-gray-800 small mb-3">Bahan Baku yang Digunakan:</h6>
                        <ul class="list-group list-group-flush">
                            @foreach($recipe->ingredients as $ingredient)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0">
                                    <span>
                                        <i class="fas fa-chevron-right text-primary fa-xs mr-2"></i>{{ $ingredient->name }}
                                    </span>
                                    <span class="badge badge-info badge-pill">
                                        {{ number_format($ingredient->pivot->quantity, 2) }} {{ $ingredient->unit }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Tambah Resep -->
    <div class="modal fade" id="addRecipeModal" tabindex="-1" role="dialog" aria-labelledby="addRecipeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('recipes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRecipeModalLabel">Buat Resep Baru</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Resep / Menu</label>
                            <input type="text" name="name" class="form-control" placeholder="cth: Caffe Latte, Matcha Frappe" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Pemetaan Bahan Baku</label>
                            <div id="add-ingredients-container">
                                <div class="row align-items-center mb-2 ingredient-row">
                                    <div class="col-md-6">
                                        <select name="ingredients[0][id]" class="form-control" required>
                                            <option value="">-- Pilih Bahan Baku --</option>
                                            @foreach($ingredients as $ing)
                                                <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.01" name="ingredients[0][quantity]" class="form-control" placeholder="Jumlah per porsi" min="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-block remove-row-btn" disabled><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-more-ingredients-btn" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-plus mr-1"></i>Tambah Bahan Baku Lainnya
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Buat Resep</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Resep -->
    <div class="modal fade" id="editRecipeModal" tabindex="-1" role="dialog" aria-labelledby="editRecipeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editRecipeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRecipeModalLabel">Edit Resep</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Resep</label>
                            <input type="text" name="name" id="edit_recipe_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Pemetaan Bahan Baku</label>
                            <div id="edit-ingredients-container">
                                <!-- Diisi oleh JavaScript -->
                            </div>
                            <button type="button" id="edit-more-ingredients-btn" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-plus mr-1"></i>Tambah Bahan Baku Lainnya
                            </button>
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
@endsection

@push('scripts')
<script>
    let ingredientsList = @json($ingredients);

    function createIngredientRow(index, selectedId = '', quantity = '') {
        let options = `<option value="">-- Pilih Bahan Baku --</option>`;
        ingredientsList.forEach(ing => {
            options += `<option value="${ing.id}" ${ing.id == selectedId ? 'selected' : ''}>${ing.name} (${ing.unit})</option>`;
        });

        return `
            <div class="row align-items-center mb-2 ingredient-row">
                <div class="col-md-6">
                    <select name="ingredients[${index}][id]" class="form-control" required>
                        ${options}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" name="ingredients[${index}][quantity]" class="form-control" value="${quantity}" placeholder="Jumlah per porsi" min="0.01" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-block remove-row-btn" onclick="this.closest('.ingredient-row').remove()"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
    }

    let addIndex = 1;
    $('#add-more-ingredients-btn').on('click', function() {
        $('#add-ingredients-container').append(createIngredientRow(addIndex));
        addIndex++;
    });

    let editIndex = 0;
    $('.edit-recipe-btn').on('click', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let mappedIngredients = $(this).data('ingredients');

        $('#edit_recipe_name').val(name);
        $('#editRecipeForm').attr('action', '/recipes/' + id);

        let container = $('#edit-ingredients-container');
        container.empty();

        editIndex = 0;
        mappedIngredients.forEach(ing => {
            container.append(createIngredientRow(editIndex, ing.id, ing.pivot.quantity));
            editIndex++;
        });

        if (mappedIngredients.length === 0) {
            container.append(createIngredientRow(editIndex));
            editIndex++;
        }
    });

    $('#edit-more-ingredients-btn').on('click', function() {
        $('#edit-ingredients-container').append(createIngredientRow(editIndex));
        editIndex++;
    });
</script>
@endpush
