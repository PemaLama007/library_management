<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Student;
use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\BookIssue;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([
                'books' => [],
                'students' => [],
                'authors' => [],
                'categories' => [],
                'publishers' => [],
                'book_issues' => []
            ]);
        }

        // Search books
        $books = Book::with(['author', 'category', 'publisher'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('author', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('category', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('publisher', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'name' => $book->name,
                    'author_name' => $book->author ? $book->author->name : 'Unknown Author',
                    'category_name' => $book->category ? $book->category->name : 'No Category',
                    'publisher_name' => $book->publisher ? $book->publisher->name : 'No Publisher'
                ];
            });

        // Search students
        $students = Student::where('name', 'LIKE', "%{$query}%")
            ->orWhere('student_id', 'LIKE', "%{$query}%")
            ->orWhere('library_card_number', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'student_id' => $student->student_id,
                    'email' => $student->email
                ];
            });

        // Search authors
        $authors = Author::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($author) {
                return [
                    'id' => $author->id,
                    'name' => $author->name
                ];
            });

        // Search categories
        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            });

        // Search publishers
        $publishers = Publisher::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($publisher) {
                return [
                    'id' => $publisher->id,
                    'name' => $publisher->name
                ];
            });

        // Search book issues
        $book_issues = BookIssue::with(['student', 'book'])
            ->whereHas('student', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('student_id', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('book', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function($issue) {
                return [
                    'id' => $issue->id,
                    'book_name' => $issue->book ? $issue->book->name : 'Unknown Book',
                    'student_name' => $issue->student ? $issue->student->name : 'Unknown Student',
                    'issue_date' => $issue->issue_date ? $issue->issue_date->format('Y-m-d') : null,
                    'return_date' => $issue->return_date ? $issue->return_date->format('Y-m-d') : null
                ];
            });

        return response()->json([
            'books' => $books,
            'students' => $students,
            'authors' => $authors,
            'categories' => $categories,
            'publishers' => $publishers,
            'book_issues' => $book_issues
        ]);
    }

    public function searchPage(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return view('search.results', [
                'query' => $query,
                'books' => collect(),
                'students' => collect(),
                'authors' => collect(),
                'categories' => collect(),
                'publishers' => collect(),
                'book_issues' => collect()
            ]);
        }

        // Detailed search for the search results page
        $books = Book::with(['author', 'category', 'publisher'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('author', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('category', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('publisher', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->paginate(20);

        $students = Student::where('name', 'LIKE', "%{$query}%")
            ->orWhere('student_id', 'LIKE', "%{$query}%")
            ->orWhere('library_card_number', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->paginate(20);

        $authors = Author::with('books')->where('name', 'LIKE', "%{$query}%")
            ->paginate(20);

        $categories = Category::with('books')->where('name', 'LIKE', "%{$query}%")
            ->paginate(20);

        $publishers = Publisher::with('books')->where('name', 'LIKE', "%{$query}%")
            ->paginate(20);

        $book_issues = BookIssue::with(['student', 'book'])
            ->where(function($subQuery) use ($query) {
                $subQuery->whereHas('student', function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('student_id', 'LIKE', "%{$query}%");
                })
                ->orWhereHas('book', function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                });
            })
            ->paginate(20);

        return view('search.results', compact(
            'query', 'books', 'students', 'authors',
            'categories', 'publishers'
        ) + ['bookIssues' => $book_issues]);
    }
}
