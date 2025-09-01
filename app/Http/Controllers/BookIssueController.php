<?php

namespace App\Http\Controllers;

use App\Models\BookIssue;
use App\Http\Requests\StoreBookIssueRequest;
use App\Http\Requests\UpdateBookIssueRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Settings;
use App\Models\Student;
use App\Services\NotificationService;
use App\Services\DynamicFineCalculator;
use \Illuminate\Http\Request;

class BookIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = BookIssue::with(['student', 'book'])->paginate(5);
        $fineCalculator = new DynamicFineCalculator();
        $bookFines = [];
        foreach ($books as $book) {
            $bookFines[$book->id] = $fineCalculator->calculateProgressiveFine(
                $book->issue_date,
                $book->return_date,
                $book->actual_return_date ?? now()->format('Y-m-d')
            );
        }
        return view('book.issueBooks', [
            'books' => $books,
            'bookFines' => $bookFines
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $students = Student::latest()->get();
        
        // Get available books (books with available copies > 0 OR books with old status system)
        $books = Book::where(function($query) {
            $query->where('available_copies', '>', 0)
                  ->orWhere('status', 'Y'); // Fallback for books not yet migrated
        })->get();
        
        return view('book.issueBook_add', [
            'students' => $students,
            'books' => $books,
            'selectedBook' => $request->input('book_id'),
            'selectedStudent' => $request->input('student_id'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBookIssueRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreBookIssueRequest $request)
    {
        $book = Book::find($request->input('book_id'));
        
        // Check if book is available using new inventory system
        if (!$book->isAvailable()) {
            return redirect()->back()->with('error', 'Book is not available for issue.');
        }
        
        $issue_date = date('Y-m-d');
        $settings = Settings::latest()->first();
        $return_days = $settings ? $settings->return_days : 7; // Default to 7 days if no settings
        $return_date = date('Y-m-d', strtotime("+" . $return_days . " days"));
        
        $bookIssue = BookIssue::create($request->validated() + [
            'student_id' => $request->input('student_id'),
            'book_id' => $request->input('book_id'),
            'issue_date' => $issue_date,
            'return_date' => $return_date,
            'issue_status' => 'N',
        ]);
        
        // Update inventory using new system
        $book->issueBook();
        
        // Schedule notifications
        $notificationService = new NotificationService();
        $notificationService->scheduleNotificationsForBookIssue($bookIssue);
        
        return redirect()->route('book_issue')->with('success', 'Book issued successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $bookIssue = BookIssue::with(['student', 'book', 'book.author', 'book.category', 'book.publisher'])->findOrFail($id);
        
        // Use Dynamic Fine Calculator
        $fineCalculator = new DynamicFineCalculator();
        $fineData = $fineCalculator->calculateProgressiveFine(
            $bookIssue->issue_date,
            $bookIssue->return_date,
            $bookIssue->actual_return_date
        );
        
        // Calculate potential remission
        $remissionData = $fineCalculator->calculateFineRemission(
            $bookIssue->student_id,
            $fineData['fine_amount']
        );
        
        return view('book.issueBook_show', [
            'bookIssue' => $bookIssue,
            'fine' => $fineData['fine_amount'],
            'fineData' => $fineData,
            'remissionData' => $remissionData,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $book = BookIssue::where('id',$id)->get()->first();
        
        // Use Dynamic Fine Calculator
        $fineCalculator = new DynamicFineCalculator();
        $fineData = $fineCalculator->calculateProgressiveFine(
            $book->issue_date,
            $book->return_date,
            $book->actual_return_date
        );
        
        return view('book.issueBook_edit', [
            'book' => $book,
            'fine' => $fineData['fine_amount'],
            'fineData' => $fineData,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $bookIssue = BookIssue::find($id);
        $book = Book::find($bookIssue->book_id);
        
        // Mark as returned
        $bookIssue->issue_status = 'Y';
        $bookIssue->return_day = now();
        $bookIssue->save();
        
        // Update inventory using new system
        $book->returnBook();
        
        // Create return confirmation notification
        $notificationService = new NotificationService();
        $notificationService->createReturnConfirmation($bookIssue);
        
        return redirect()->route('book_issue')->with('success', 'Book returned successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookIssue  $book_issue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        BookIssue::find($id)->delete();
        return redirect()->route('book_issue');
    }
}
