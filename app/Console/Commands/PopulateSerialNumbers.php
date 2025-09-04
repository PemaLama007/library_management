<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\BookIssue;
use App\Models\Student;

class PopulateSerialNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:populate-serial-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate serial numbers for all models';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Populating serial numbers for all models...');

        // Populate Books
        $this->info('Populating Books...');
        $books = Book::orderBy('id')->get();
        $serialNumber = 1;
        foreach ($books as $book) {
            $book->update(['serial_number' => $serialNumber]);
            $serialNumber++;
        }
        $this->info("Updated {$books->count()} books");

        $this->info('Serial number population completed successfully!');
        
        return 0;
    }
}
