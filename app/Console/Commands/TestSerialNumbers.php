<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\BookIssue;
use App\Models\Student;

class TestSerialNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:test-serial-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test serial number functionality for all models';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧪 Testing Serial Number System');
        $this->info('===============================');
        $this->newLine();

        try {
            // Test Books
            $this->info('📚 Books:');
            $books = Book::orderBy('id')->get();
            foreach ($books as $book) {
                $this->line("   ID: {$book->id}, Serial: {$book->serial_number}, Name: {$book->name}");
            }
            $this->newLine();

            // Test Authors
            $this->info('✍️ Authors:');
            $authors = Author::orderBy('id')->get();
            foreach ($authors as $author) {
                $this->line("   ID: {$author->id}, Serial: {$author->serial_number}, Name: {$author->name}");
            }
            $this->newLine();

            // Test Publishers
            $this->info('🏢 Publishers:');
            $publishers = Publisher::orderBy('id')->get();
            foreach ($publishers as $publisher) {
                $this->line("   ID: {$publisher->id}, Serial: {$publisher->serial_number}, Name: {$publisher->name}");
            }
            $this->newLine();

            // Test Categories
            $this->info('📂 Categories:');
            $categories = Category::orderBy('id')->get();
            foreach ($categories as $category) {
                $this->line("   ID: {$category->id}, Serial: {$category->serial_number}, Name: {$category->name}");
            }
            $this->newLine();

            // Test Students
            $this->info('👨‍🎓 Students:');
            $students = Student::orderBy('id')->get();
            foreach ($students as $student) {
                $this->line("   ID: {$student->id}, Serial: {$student->serial_number}, Name: {$student->name}");
            }
            $this->newLine();

            // Test Book Issues
            $this->info('📖 Book Issues:');
            $bookIssues = BookIssue::orderBy('id')->get();
            foreach ($bookIssues as $issue) {
                $this->line("   ID: {$issue->id}, Serial: {$issue->serial_number}, Book: {$issue->book->name}, Student: {$issue->student->name}");
            }
            $this->newLine();

            $this->info('✅ All serial number tests completed successfully!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
