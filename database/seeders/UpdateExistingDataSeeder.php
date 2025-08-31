<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Student;
use App\Models\BookIssue;

class UpdateExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update existing books with inventory data
        $this->updateBooksInventory();
        
        // Update existing students with student IDs
        $this->updateStudentIds();
        
        $this->command->info('Existing data updated successfully!');
    }
    
    /**
     * Update books with inventory information
     */
    private function updateBooksInventory()
    {
        $books = book::whereNull('total_copies')->get();
        
        foreach ($books as $book) {
            // Calculate how many times this book is currently issued (not returned)
            $currentlyIssued = BookIssue::where('book_id', $book->id)
                ->where('issue_status', 'N')
                ->count();
            
            // Set default inventory based on current usage
            $totalCopies = max(1, $currentlyIssued + 1); // At least 1 copy, plus currently issued
            $availableCopies = max(0, $totalCopies - $currentlyIssued);
            
            $book->update([
                'total_copies' => $totalCopies,
                'available_copies' => $availableCopies,
                'isbn' => 'ISBN-' . str_pad($book->id, 10, '0', STR_PAD_LEFT), // Generate sample ISBN
                'description' => 'Book description for ' . $book->name
            ]);
        }
        
        $this->command->info("Updated {$books->count()} books with inventory data.");
    }
    
    /**
     * Update students with student IDs
     */
    private function updateStudentIds()
    {
        $students = student::whereNull('student_id')->get();
        
        foreach ($students as $student) {
            $student->update([
                'student_id' => $student->generateStudentId(),
                'library_card_number' => $student->generateLibraryCardNumber(),
                'enrollment_date' => $student->created_at ?? now(),
                'status' => 'active'
            ]);
        }
        
        $this->command->info("Updated {$students->count()} students with student IDs.");
    }
}
