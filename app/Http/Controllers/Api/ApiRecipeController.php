<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiRecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('ingredients')->get();
        return response()->json($recipes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|gt:0',
        ]);

        $recipe = DB::transaction(function () use ($request) {
            $rec = Recipe::create(['name' => $request->name]);

            $syncData = [];
            foreach ($request->ingredients as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity']];
            }
            $rec->ingredients()->sync($syncData);

            return $rec->load('ingredients');
        });

        return response()->json($recipe, 201);
    }

    public function show(Recipe $recipe)
    {
        return response()->json($recipe->load('ingredients'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|gt:0',
        ]);

        DB::transaction(function () use ($request, $recipe) {
            $recipe->update(['name' => $request->name]);

            $syncData = [];
            foreach ($request->ingredients as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity']];
            }
            $recipe->ingredients()->sync($syncData);
        });

        return response()->json($recipe->load('ingredients'));
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return response()->json([
            'message' => 'Recipe deleted successfully'
        ]);
    }
}
