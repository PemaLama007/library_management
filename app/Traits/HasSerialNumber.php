<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait HasSerialNumber
{
    /**
     * Boot the trait
     */
    protected static function bootHasSerialNumber()
    {
        static::deleted(function (Model $model) {
            static::reorderSerialNumbers();
        });
    }

    /**
     * Get serial number for display
     */
    public function getSerialNumberAttribute()
    {
        return static::where('id', '<=', $this->id)->count();
    }

    /**
     * Reorder serial numbers after deletion
     */
    public static function reorderSerialNumbers()
    {
        // This is a placeholder for the reordering logic
        // In a real implementation, you might want to add a separate serial_number column
        // For now, we'll use the count approach in the getSerialNumberAttribute method
    }

    /**
     * Scope to get items with proper serial numbers
     */
    public function scopeWithSerialNumber($query)
    {
        return $query->select('*')
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY id) as serial_number');
    }
}
