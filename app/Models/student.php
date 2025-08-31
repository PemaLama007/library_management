<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'enrollment_date' => 'date',
    ];

    /**
     * Get the book issues for this student
     */
    public function bookIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    /**
     * Get the notifications for this student
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get currently issued books
     */
    public function currentlyIssuedBooks(): HasMany
    {
        return $this->bookIssues()->where('issue_status', 'N');
    }

    /**
     * Generate unique student ID
     */
    public static function generateStudentId(): string
    {
        $year = date('Y');
        $lastStudent = self::where('student_id', 'like', "STD{$year}%")
                          ->orderBy('student_id', 'desc')
                          ->first();
        
        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "STD{$year}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique library card number
     */
    public static function generateLibraryCardNumber(): string
    {
        $prefix = 'LIB';
        $lastCard = self::where('library_card_number', 'like', "{$prefix}%")
                       ->orderBy('library_card_number', 'desc')
                       ->first();
        
        if ($lastCard) {
            $lastNumber = (int) substr($lastCard->library_card_number, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1001;
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Check if student is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
