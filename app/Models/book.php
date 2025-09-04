<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasSerialNumber;
use Illuminate\Support\Facades\Schema;

class Book extends Model
{
    use HasFactory, HasSerialNumber;
    protected $guarded = [];

    /**
     * Get the author that owns the book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class,'author_id','id');
    }

    /**
     * Get the category that owns the book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(category::class);
    }

    /**
     * Get the publisher that owns the book
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(publisher::class);
    }

    /**
     * Get the book issues for this book
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    /**
     * Check if book is available for issue
     */
    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }

    /**
     * Get currently issued copies count
     */
    public function getIssuedCopiesAttribute(): int
    {
        return $this->bookIssues()->where('issue_status', 'N')->count();
    }

    /**
     * Update available copies after issue
     */
    public function issueBook(): bool
    {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
            return true;
        }
        return false;
    }

    /**
     * Update available copies after return
     */
    public function returnBook(): bool
    {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
            return true;
        }
        return false;
    }

    /**
     * Get serial number for display
     */
    public function getSerialNumberAttribute()
    {
        // Check if serial_number column exists and has a value
        if (isset($this->attributes['serial_number']) && $this->attributes['serial_number']) {
            return $this->attributes['serial_number'];
        }
        
        // Fallback to count-based approach (works even without the column)
        return static::where('id', '<=', $this->id)->count();
    }

    /**
     * Boot method to handle serial number assignment
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($book) {
            // Only set serial_number if the column exists
            if (Schema::hasColumn('books', 'serial_number') && !isset($book->serial_number)) {
                $maxSerial = static::max('serial_number') ?? 0;
                $book->serial_number = $maxSerial + 1;
            }
        });
        
        static::deleted(function ($book) {
            // Only reorder if the column exists
            if (Schema::hasColumn('books', 'serial_number')) {
                static::reorderSerialNumbers();
            }
        });
    }

    /**
     * Reorder serial numbers after deletion
     */
    public static function reorderSerialNumbers()
    {
        $books = static::orderBy('id')->get();
        $serialNumber = 1;
        
        foreach ($books as $book) {
            $book->update(['serial_number' => $serialNumber]);
            $serialNumber++;
        }
    }
}
