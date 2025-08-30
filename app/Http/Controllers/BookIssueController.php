<?php

namespace App\Http\Controllers;

use App\Models\book_issue;
use App\Http\Requests\Storebook_issueRequest;
use App\Http\Requests\Updatebook_issueRequest;
use App\Models\auther;
use App\Models\book;
use App\Models\settings;
use App\Models\student;
use App\Services\NotificationService;
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
        return view('book.issueBooks', [
            'books' => book_issue::Paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $students = student::latest()->get();
        
        // Get available books (books with available copies > 0 OR books with old status system)
        $books = book::where(function($query) {
            $query->where('available_copies', '>', 0)
                  ->orWhere('status', 'Y'); // Fallback for books not yet migrated
        })->get();
        
        return view('book.issueBook_add', [
            'students' => $students,
            'books' => $books,
            'selectedBook' => $request->book_id,
            'selectedStudent' => $request->student_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Storebook_issueRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Storebook_issueRequest $request)
    {
        $book = book::find($request->book_id);
        
        // Check if book is available using new inventory system
        if (!$book->isAvailable()) {
            return redirect()->back()->with('error', 'Book is not available for issue.');
        }
        
        $issue_date = date('Y-m-d');
        $settings = settings::latest()->first();
        $return_days = $settings ? $settings->return_days : 7; // Default to 7 days if no settings
        $return_date = date('Y-m-d', strtotime("+" . $return_days . " days"));
        
        $bookIssue = book_issue::create($request->validated() + [
            'student_id' => $request->student_id,
            'book_id' => $request->book_id,
            'issue_date' => $issue_date,
            'return_date' => $return_date,
            'issue_status' => 'N',
        ]);
        
        // Update inventory using new system
        $book->issueBook();
        
        // Schedule notifications
        $notificationService = new NotificationService();
        $notificationService->scheduleNotificationsForBookIssue($bookIssue);
        
        return redirect()->route('book_issued')->with('success', 'Book issued successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $bookIssue = book_issue::with(['student', 'book', 'book.auther', 'book.category', 'book.publisher'])->findOrFail($id);
        
        // Calculate fine if applicable
        $currentDate = date_create(date('Y-m-d'));
        $returnDate = date_create($bookIssue->return_date->format('Y-m-d'));
        $fine = 0;
        
        if ($currentDate > $returnDate && $bookIssue->issue_status == 'N') {
            $diff = date_diff($returnDate, $currentDate);
            $overdueDays = $diff->format('%a');
            $settings = settings::latest()->first();
            $finePerDay = $settings ? $settings->fine : 0;
            $fine = $overdueDays * $finePerDay;
        }
        
        return view('book.issueBook_show', [
            'bookIssue' => $bookIssue,
            'fine' => $fine,
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
        // calculate the total fine  (total days * fine per day)
        $book = book_issue::where('id',$id)->get()->first();
        $currentDate = date_create(date('Y-m-d'));
        $returnDate = date_create($book->return_date->format('Y-m-d'));
        $fine = 0;
        
        if ($currentDate > $returnDate && $book->issue_status == 'N') {
            $diff = date_diff($returnDate, $currentDate);
            $overdueDays = $diff->format('%a');
            $settings = settings::latest()->first();
            $finePerDay = $settings ? $settings->fine : 0;
            $fine = $overdueDays * $finePerDay;
        }
        
        return view('book.issueBook_edit', [
            'book' => $book,
            'fine' => $fine,
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
        $bookIssue = book_issue::find($id);
        $book = book::find($bookIssue->book_id);
        
        // Mark as returned
        $bookIssue->issue_status = 'Y';
        $bookIssue->return_day = now();
        $bookIssue->save();
        
        // Update inventory using new system
        $book->returnBook();
        
        // Create return confirmation notification
        $notificationService = new NotificationService();
        $notificationService->createReturnConfirmation($bookIssue);
        
        return redirect()->route('book_issued')->with('success', 'Book returned successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\book_issue  $book_issue
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        book_issue::find($id)->delete();
        return redirect()->route('book_issued');
    }
}
