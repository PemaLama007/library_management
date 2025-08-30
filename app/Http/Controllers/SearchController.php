<?php

namespace App\Http\Controllers;

use App\Models\book;
use App\Models\student;
use App\Models\auther;
use App\Models\category;
use App\Models\publisher;
use App\Models\book_issue;
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
        $books = book::with(['auther', 'category', 'publisher'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('auther', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('category', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('publisher', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

        // Search students
        $students = student::where('name', 'LIKE', "%{$query}%")
            ->orWhere('student_id', 'LIKE', "%{$query}%")
            ->orWhere('library_card_number', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        // Search authors
        $authors = auther::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();

        // Search categories
        $categories = category::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();

        // Search publishers
        $publishers = publisher::where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get();

        // Search book issues
        $book_issues = book_issue::with(['student', 'book'])
            ->whereHas('student', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('student_id', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('book', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get();

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
        $books = book::with(['auther', 'category', 'publisher'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhereHas('auther', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('category', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('publisher', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->paginate(20);

        $students = student::where('name', 'LIKE', "%{$query}%")
            ->orWhere('student_id', 'LIKE', "%{$query}%")
            ->orWhere('library_card_number', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->paginate(20);

        $authors = auther::where('name', 'LIKE', "%{$query}%")
            ->paginate(20);

        $categories = category::where('name', 'LIKE', "%{$query}%")
            ->paginate(20);

        $publishers = publisher::where('name', 'LIKE', "%{$query}%")
            ->paginate(20);

        $book_issues = book_issue::with(['student', 'book'])
            ->whereHas('student', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('student_id', 'LIKE', "%{$query}%");
            })
            ->orWhereHas('book', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->paginate(20);

        return view('search.results', compact(
            'query', 'books', 'students', 'authors', 
            'categories', 'publishers', 'book_issues'
        ));
    }
}
