<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'unit_id',
        'stock',
        'minimum_stock',
    ];

    public function unitRelation()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_ingredient')
                    ->withPivot('stock', 'minimum_stock')
                    ->withTimestamps();
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function wastes()
    {
        return $this->hasMany(Waste::class);
    }

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredient')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function adjustStock($outletId, $type, $quantity, $description)
    {
        $pivot = DB::table('outlet_ingredient')
            ->where('ingredient_id', $this->id)
            ->where('outlet_id', $outletId)
            ->first();

        $currentOutletStock = $pivot ? $pivot->stock : 0;
        $currentOutletMinStock = $pivot ? $pivot->minimum_stock : $this->minimum_stock;

        if ($type === 'in') {
            $newOutletStock = $currentOutletStock + $quantity;
            $newGlobalStock = $this->stock + $quantity;
        } else {
            $newOutletStock = $currentOutletStock - $quantity;
            $newGlobalStock = $this->stock - $quantity;
        }

        DB::table('outlet_ingredient')->updateOrInsert(
            ['ingredient_id' => $this->id, 'outlet_id' => $outletId],
            ['stock' => $newOutletStock, 'minimum_stock' => $currentOutletMinStock, 'updated_at' => now(), 'created_at' => $pivot ? $pivot->created_at ?? now() : now()]
        );

        $this->update(['stock' => $newGlobalStock]);

        return StockMovement::create([
            'ingredient_id' => $this->id,
            'outlet_id' => $outletId,
            'type' => $type,
            'quantity' => $quantity,
            'description' => $description,
        ]);
    }
}
