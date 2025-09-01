<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookIssueController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


Route::get('/', function () {
    return view('welcome');
})->middleware('guest');
Route::post('/', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public search routes (accessible without authentication)
Route::get('/search', [SearchController::class, 'searchPage'])->name('search');
Route::get('/search/ajax', [SearchController::class, 'globalSearch'])->name('search.ajax');

Route::middleware('auth')->group(function () {
    Route::get('change-password',[DashboardController::class,'change_password_view'])->name('change_password_view');
    Route::post('change-password',[DashboardController::class,'change_password'])->name('change_password');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // author CRUD
    Route::get('/authors', [AuthorController::class, 'index'])->name('authors');
    Route::get('/authors/create', [AuthorController::class, 'create'])->name('authors.create');
    Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('authors.show');
    Route::get('/authors/edit/{author}', [AuthorController::class, 'edit'])->name('authors.edit');
    Route::post('/authors/update/{id}', [AuthorController::class, 'update'])->name('authors.update');
    Route::post('/authors/delete/{id}', [AuthorController::class, 'destroy'])->name('authors.destroy');
    Route::post('/authors/create', [AuthorController::class, 'store'])->name('authors.store');

    // publisher crud
    Route::get('/publishers', [PublisherController::class, 'index'])->name('publishers');
    Route::get('/publishers/create', [PublisherController::class, 'create'])->name('publishers.create');
    Route::get('/publishers/{publisher}', [PublisherController::class, 'show'])->name('publishers.show');
    Route::get('/publishers/edit/{publisher}', [PublisherController::class, 'edit'])->name('publishers.edit');
    Route::post('/publishers/update/{id}', [PublisherController::class, 'update'])->name('publishers.update');
    Route::post('/publishers/delete/{id}', [PublisherController::class, 'destroy'])->name('publishers.destroy');
    Route::post('/publishers/create', [PublisherController::class, 'store'])->name('publishers.store');

    // Category CRUD
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/categories/edit/{category}', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/create', [CategoryController::class, 'store'])->name('categories.store');




    // books CRUD
    Route::get('/books', [BookController::class, 'index'])->name('books');
    Route::get('/book/create', [BookController::class, 'create'])->name('book.create');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/book/edit/{book}', [BookController::class, 'edit'])->name('books.edit');
    Route::post('/book/update/{id}', [BookController::class, 'update'])->name('book.update');
    Route::post('/book/delete/{id}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::post('/book/create', [BookController::class, 'store'])->name('book.store');

    // students CRUD
    Route::get('/students', [StudentController::class, 'index'])->name('students');
    Route::get('/student/create', [StudentController::class, 'create'])->name('student.create');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/student/edit/{student}', [StudentController::class, 'edit'])->name('student.edit');
    Route::post('/student/update/{id}', [StudentController::class, 'update'])->name('student.update');
    Route::post('/student/delete/{id}', [StudentController::class, 'destroy'])->name('student.destroy');
    Route::post('/student/create', [StudentController::class, 'store'])->name('student.store');
    Route::get('/student/show/{id}', [StudentController::class, 'show'])->name('student.show');



    Route::get('/book_issue', [BookIssueController::class, 'index'])->name('book_issue');
    Route::get('/book-issue/create', [BookIssueController::class, 'create'])->name('book_issue.create');
    Route::post('/book-issue/create', [BookIssueController::class, 'store'])->name('book_issue.store');
    Route::get('/book-issue/edit/{id}', [BookIssueController::class, 'edit'])->name('book_issue.edit');
    Route::post('/book-issue/update/{id}', [BookIssueController::class, 'update'])->name('book_issue.update');
    Route::post('/book-issue/delete/{id}', [BookIssueController::class, 'destroy'])->name('book_issue.destroy');
    Route::get('/book-issue/{id}', [BookIssueController::class, 'show'])->name('book_issue.show');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/Date-Wise', [ReportsController::class, 'date_wise'])->name('reports.date_wise');
    Route::post('/reports/Date-Wise', [ReportsController::class, 'generate_date_wise_report'])->name('reports.date_wise_generate');
    Route::get('/reports/monthly-Wise', [ReportsController::class, 'month_wise'])->name('reports.month_wise');
    Route::post('/reports/monthly-Wise', [ReportsController::class, 'generate_month_wise_report'])->name('reports.month_wise_generate');
    Route::get('/reports/not-returned', [ReportsController::class, 'not_returned'])->name('reports.not_returned');
    Route::get('/reports/inventory', [ReportsController::class, 'inventory'])->name('reports.inventory');
    
    // Export routes
    Route::get('/reports/export/{format}', [ReportsController::class, 'export'])->name('reports.export');
    Route::get('/reports/export/{type}/{format}', [ReportsController::class, 'exportSpecific'])->name('reports.export.specific');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
});
