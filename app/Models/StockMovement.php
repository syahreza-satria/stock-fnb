<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ingredient_id',
        'type',
        'quantity',
        'description',
        'created_at',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
