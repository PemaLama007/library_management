<?php

namespace App\Http\Controllers;

use App\Models\Author;

class AuthorController extends Controller
{
    public function index()
    {
        return view('author.index', [
            'authors' => Author::Paginate(5)
        ]);
    }

    public function create()
    {
        return view('author.create');
    }

    public function store()
    {
        $validated = request()->validate([
            'name' => 'required|min:3'
        ]);
        
        Author::create($validated);
        return redirect()->route('authors');
    }

    public function edit(Author $author)
    {
        return view('author.edit', [
            'author' => $author
        ]);
    }

    public function show(Author $author)
    {
        return view('author.show', [
            'author' => $author
        ]);
    }

    public function update($id)
    {
        $validated = request()->validate([
            'name' => 'required|min:3'
        ]);
        
        $author = Author::find($id);
        $author->name = $validated['name'];
        $author->save();

        return redirect()->route('authors');
    }

    public function destroy($id)
    {
        try {
            $author = Author::findOrFail($id);
            
            // Check if author has books with active book issues (status 'N' means still issued)
            $booksWithActiveIssues = $author->books()->whereHas('bookIssues', function($query) {
                $query->where('issue_status', 'N');
            })->get();
            
            if ($booksWithActiveIssues->count() > 0) {
                $bookNames = $booksWithActiveIssues->pluck('name')->join(', ');
                return redirect()->route('authors')
                    ->with('error', "Cannot delete author '{$author->name}' because the following books have active issues: {$bookNames}. Please return all books first.");
            }
            
            // Check if author has books (without active issues)
            $books = $author->books()->get();
            if ($books->count() > 0) {
                // Delete books first (this will cascade delete any returned book issues)
                foreach ($books as $book) {
                    $book->delete();
                }
            }
            
            // Now delete the author
            $author->delete();
            
            return redirect()->route('authors')
                ->with('success', "Author '{$author->name}' has been deleted successfully.");
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle any remaining foreign key constraint violations
            if ($e->getCode() == '23000') {
                return redirect()->route('authors')
                    ->with('error', 'Cannot delete this author because they have associated books with active issues. Please ensure all books are returned first.');
            }
            
            return redirect()->route('authors')
                ->with('error', 'An error occurred while deleting the author: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->route('authors')
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
