<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasSerialNumber;

class Publisher extends Model
{
    use HasFactory, HasSerialNumber;
    protected $guarded = [];

    /**
     * Get the books for this publisher
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books(): HasMany
    {
        return $this->hasMany(book::class, 'publisher_id', 'id');
    }
}
