<?php

namespace App\Http\Controllers;

use App\Http\Requests\changePasswordRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get category data for chart
        $categoryData = Category::leftJoin('books', 'categories.id', '=', 'books.category_id')
            ->selectRaw('categories.name, COUNT(books.id) as book_count')
            ->groupBy('categories.id', 'categories.name')
            ->get();

        return view('dashboard', [
            'authors' => Author::count(),
            'publishers' => Publisher::count(),
            'categories' => Category::count(),
            'books' => Book::count(),
            'students' => Student::count(),
            'issued_books' => BookIssue::count(),
            'category_data' => $categoryData,
        ]);
    }

    public function change_password_view()
    {
        return view('reset_password');
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'c_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (password_verify($request->c_password, $user->password)) {
            $user->password = bcrypt($request->password);
            $user->save();
            return redirect()->route("dashboard")->with('success', 'Password changed successfully');
        } else {
            return redirect()->back()->withErrors(['c_password' => 'Old password is incorrect']);
        }
    }
}
