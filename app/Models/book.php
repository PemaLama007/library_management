<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;
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
}
