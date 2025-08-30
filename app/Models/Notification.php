<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'book_issue_id', 
        'type',
        'title',
        'message',
        'status',
        'scheduled_at',
        'sent_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the student that owns the notification
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(student::class);
    }

    /**
     * Get the book issue associated with the notification
     */
    public function bookIssue(): BelongsTo
    {
        return $this->belongsTo(book_issue::class, 'book_issue_id');
    }
}
