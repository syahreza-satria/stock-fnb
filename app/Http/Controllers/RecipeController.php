<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function index()
    {
        $recipes = Recipe::with('ingredients')->get();
        $ingredients = Ingredient::all();
        return view('recipes.index', compact('recipes', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ingredients' => 'required|array',
            'ingredients.*.id' => 'required|exists:ingredients,id',
            'ingredients.*.quantity' => 'required|numeric|gt:0',
        ]);

        DB::transaction(function () use ($request) {
            $recipe = Recipe::create(['name' => $request->name]);

            $syncData = [];
            foreach ($request->ingredients as $item) {
                $syncData[$item['id']] = ['quantity' => $item['quantity']];
            }
            $recipe->ingredients()->sync($syncData);
        });

        return redirect()->route('recipes.index')->with('success', 'Resep berhasil dibuat!');
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

        return redirect()->route('recipes.index')->with('success', 'Resep berhasil diperbarui!');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('recipes.index')->with('success', 'Resep berhasil dihapus!');
    }
}
