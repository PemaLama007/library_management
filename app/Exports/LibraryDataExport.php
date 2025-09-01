<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\Student;
use App\Models\BookIssue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LibraryDataExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Books' => new BooksExport(),
            'Authors' => new AuthorsExport(),
            'Publishers' => new PublishersExport(),
            'Categories' => new CategoriesExport(),
            'Students' => new StudentsExport(),
            'Book Issues' => new BookIssuesExport(),
        ];
    }
}

class BooksExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Book::with(['author', 'category', 'publisher'])
            ->orderBy('name')
            ->get()
            ->map(function ($book) {
                return [
                    'ID' => $book->id,
                    'Book Name' => $book->name,
                    'Author' => $book->author->name ?? 'N/A',
                    'Category' => $book->category->name ?? 'N/A',
                    'Publisher' => $book->publisher->name ?? 'N/A',
                    'ISBN' => $book->isbn ?? 'N/A',
                    'Total Copies' => $book->total_copies ?? 1,
                    'Available Copies' => $book->available_copies ?? 1,
                    'Currently Issued' => $book->bookIssues()->where('issue_status', 'N')->count(),
                    'Status' => $book->status == 'Y' ? 'Active' : 'Inactive',
                    'Created Date' => $book->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID', 'Book Name', 'Author', 'Category', 'Publisher', 
            'ISBN', 'Total Copies', 'Available Copies', 'Currently Issued', 
            'Status', 'Created Date'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class AuthorsExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Author::withCount('books')
            ->orderBy('name')
            ->get()
            ->map(function ($author) {
                return [
                    'ID' => $author->id,
                    'Author Name' => $author->name,
                    'Total Books' => $author->books_count,
                    'Created Date' => $author->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Author Name', 'Total Books', 'Created Date'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class PublishersExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Publisher::withCount('books')
            ->orderBy('name')
            ->get()
            ->map(function ($publisher) {
                return [
                    'ID' => $publisher->id,
                    'Publisher Name' => $publisher->name,
                    'Total Books' => $publisher->books_count,
                    'Created Date' => $publisher->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Publisher Name', 'Total Books', 'Created Date'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class CategoriesExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Category::withCount('books')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'ID' => $category->id,
                    'Category Name' => $category->name,
                    'Total Books' => $category->books_count,
                    'Created Date' => $category->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return ['ID', 'Category Name', 'Total Books', 'Created Date'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class StudentsExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return Student::withCount('bookIssues')
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $currentIssues = $student->bookIssues()->where('issue_status', 'N')->count();
                $overdueIssues = $student->bookIssues()
                    ->where('issue_status', 'N')
                    ->where('return_date', '<', now())
                    ->count();

                return [
                    'ID' => $student->id,
                    'Student ID' => $student->student_id,
                    'Student Name' => $student->name,
                    'Email' => $student->email,
                    'Phone' => $student->phone,
                    'Address' => $student->address ?? 'N/A',
                    'Library Card' => $student->library_card_number ?? 'N/A',
                    'Total Issues' => $student->book_issues_count,
                    'Current Issues' => $currentIssues,
                    'Overdue Issues' => $overdueIssues,
                    'Registration Date' => $student->created_at->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID', 'Student ID', 'Student Name', 'Email', 'Phone',
            'Address', 'Library Card', 'Total Issues', 'Current Issues',
            'Overdue Issues', 'Registration Date'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class BookIssuesExport implements FromCollection, WithHeadings, WithStyles
{
    public function collection()
    {
        return BookIssue::with(['book', 'student'])
            ->orderBy('issue_date', 'desc')
            ->get()
            ->map(function ($issue) {
                $isOverdue = $issue->issue_status == 'N' && $issue->return_date < now();
                $overdueDays = $isOverdue ? now()->diffInDays($issue->return_date) : 0;

                return [
                    'Issue ID' => $issue->id,
                    'Book Name' => $issue->book->name ?? 'N/A',
                    'Student Name' => $issue->student->name ?? 'N/A',
                    'Student ID' => $issue->student->student_id ?? 'N/A',
                    'Issue Date' => $issue->issue_date->format('Y-m-d'),
                    'Return Date' => $issue->return_date->format('Y-m-d'),
                    'Actual Return Date' => $issue->actual_return_date ? $issue->actual_return_date->format('Y-m-d') : 'Not Returned',
                    'Status' => $issue->issue_status == 'Y' ? 'Returned' : 'Issued',
                    'Is Overdue' => $isOverdue ? 'Yes' : 'No',
                    'Overdue Days' => $overdueDays,
                    'Student Phone' => $issue->student->phone ?? 'N/A',
                    'Student Email' => $issue->student->email ?? 'N/A',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Issue ID', 'Book Name', 'Student Name', 'Student ID', 'Issue Date',
            'Return Date', 'Actual Return Date', 'Status', 'Is Overdue',
            'Overdue Days', 'Student Phone', 'Student Email'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
