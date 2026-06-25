<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_unit_id',
        'to_unit_id',
        'factor',
    ];

    public function fromUnit()
    {
        return $this->belongsTo(Unit::class, 'from_unit_id');
    }

    public function toUnit()
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }

    /**
     * Helper to convert quantity from one unit to another.
     * Returns converted quantity if rule exists, otherwise returns original quantity.
     */
    public static function convert($quantity, $fromUnitId, $toUnitId)
    {
        if ($fromUnitId == $toUnitId) {
            return $quantity;
        }

        // Try direct rule: e.g. kg -> g (factor 1000) => quantity * 1000
        $conversion = self::where('from_unit_id', $fromUnitId)
                          ->where('to_unit_id', $toUnitId)
                          ->first();

        if ($conversion) {
            return $quantity * $conversion->factor;
        }

        // Try inverse rule: e.g. g -> kg (rule exists for kg -> g with factor 1000) => quantity / 1000
        $inverseConversion = self::where('from_unit_id', $toUnitId)
                               ->where('to_unit_id', $fromUnitId)
                               ->first();

        if ($inverseConversion && $inverseConversion->factor != 0) {
            return $quantity / $inverseConversion->factor;
        }

        return $quantity;
    }
}
