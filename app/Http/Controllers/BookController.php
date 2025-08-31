<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\StorebookRequest;
use App\Http\Requests\UpdatebookRequest;
use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('book.index', [
            'books' => Book::Paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('book.create',[
            'authors' => Author::latest()->get(),
            'publishers' => Publisher::latest()->get(),
            'categories' => Category::latest()->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorebookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorebookRequest $request)
    {
        $validated = $request->validated();
        
        // Set default values for optional fields
        $validated['status'] = 'Y';
        $validated['total_copies'] = $validated['total_copies'] ?? 1;
        $validated['available_copies'] = $validated['available_copies'] ?? $validated['total_copies'];
        
        Book::create($validated);
        
        return redirect()->route('books')
            ->with('success', 'Book added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(book $book)
    {
        return view('book.show', [
            'book' => $book
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        return view('book.edit',[
            'authors' => Author::latest()->get(),
            'publishers' => Publisher::latest()->get(),
            'categories' => Category::latest()->get(),
            'book' => $book
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatebookRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdatebookRequest $request, $id)
    {
        $book = Book::find($id);
        $validated = $request->validated();
        
        // Calculate current issued copies
        $currentlyIssued = $book->bookIssues()->where('issue_status', 'N')->count();
        
        // Validate that new total copies is not less than currently issued
        $newTotalCopies = $validated['total_copies'] ?? $book->total_copies;
        if ($newTotalCopies < $currentlyIssued) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Cannot set total copies to {$newTotalCopies}. {$currentlyIssued} copies are currently issued.");
        }
        
        // Update book details
        $book->name = $validated['name'];
        $book->author_id = $validated['author_id'];
        $book->category_id = $validated['category_id'];
        $book->publisher_id = $validated['publisher_id'];
        $book->isbn = $validated['isbn'] ?? $book->isbn;
        $book->description = $validated['description'] ?? $book->description;
        $book->total_copies = $newTotalCopies;
        $book->available_copies = $newTotalCopies - $currentlyIssued;
        
        $book->save();
        
        return redirect()->route('books')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        
        // Check if book has any active issues
        $activeIssues = $book->bookIssues()->where('issue_status', 'N')->count();
        if ($activeIssues > 0) {
            return redirect()->route('books')
                ->with('error', "Cannot delete book. {$activeIssues} copies are currently issued.");
        }
        
        $book->delete();
        return redirect()->route('books')
            ->with('success', 'Book deleted successfully!');
    }
}
