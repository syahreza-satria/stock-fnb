<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'outlet_ingredient')
                    ->withPivot('stock', 'minimum_stock')
                    ->withTimestamps();
    }
}
