@extends('layouts.app')

@section('content')
<div class="search-container">
    <div class="search-header">
        <h1 class="search-title">Search Results</h1>
        <p class="search-subtitle">{{ isset($query) ? "Results for: \"$query\"" : 'Search the library database' }}</p>
        
        <form action="{{ route('search') }}" method="GET" class="search-form">
            <input
                type="text"
                name="q"
                class="form-control search-input"
                placeholder="Search books, authors, students..."
                value="{{ request('q') }}"
                autocomplete="off"
            >
            <button class="btn search-btn" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    @if(isset($query) && $query)
        @if(!isset($books) && !isset($authors) && !isset($students) && !isset($categories) && !isset($publishers) && !isset($bookIssues))
            <div class="search-section">
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>No results found</h3>
                    <p>Try searching with different keywords or check your spelling.</p>
                </div>
            </div>
        @else
            <!-- Books Results -->
            @if(isset($books) && $books->count() > 0)
            <div class="search-section">
                <div class="section-header">
                    <h4 class="section-title">
                        <span><i class="fas fa-book me-2"></i>Books</span>
                        <span class="section-count">{{ $books->count() }} results</span>
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Publisher</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                            <tr>
                                <td><strong>#{{ $book->id }}</strong></td>
                                <td>
                                    <a href="{{ route('books.show', $book->id) }}" class="book-link">
                                        {{ $book->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($book->author)
                                        <a href="{{ route('authors.show', $book->author->id) }}" class="author-link">
                                            {{ $book->author->name }}
                                        </a>
                                    @else
                                        <span class="no-data-text">No author</span>
                                    @endif
                                </td>
                                <td>
                                    @if($book->category)
                                        <a href="{{ route('categories.show', $book->category->id) }}" class="category-link">
                                            {{ $book->category->name }}
                                        </a>
                                    @else
                                        <span class="no-data-text">No category</span>
                                    @endif
                                </td>
                                <td>
                                    @if($book->publisher)
                                        <a href="{{ route('publishers.show', $book->publisher->id) }}" class="publisher-link">
                                            {{ $book->publisher->name }}
                                        </a>
                                    @else
                                        <span class="no-data-text">No publisher</span>
                                    @endif
                                </td>
                                <td>
                                    @if($book->status == 'Y')
                                        <span class="status-badge status-available">Available</span>
                                    @else
                                        <span class="status-badge status-unavailable">Unavailable</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('books.show', $book->id) }}" class="action-btn btn-view">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('books.edit', $book->id) }}" class="action-btn btn-edit">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    @if($book->status == 'Y')
                                        <a href="{{ route('book_issue.create', ['book_id' => $book->id]) }}" class="action-btn btn-issue">
                                            <i class="fas fa-hand-holding me-1"></i>Issue
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Students Results -->
            @if(isset($students) && $students->count() > 0)
            <div class="search-section">
                <div class="section-header">
                    <h4 class="section-title">
                        <span><i class="fas fa-user-graduate me-2"></i>Students</span>
                        <span class="section-count">{{ $students->count() }} results</span>
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td><strong>#{{ $student->id }}</strong></td>
                                <td>
                                    <a href="{{ route('students.show', $student->id) }}" class="student-link">
                                        {{ $student->name }}
                                    </a>
                                </td>
                                <td>{{ $student->email ?? 'No email' }}</td>
                                <td>{{ $student->phone ?? 'No phone' }}</td>
                                <td>{{ $student->class ?? 'No class' }}</td>
                                <td>
                                    <a href="{{ route('students.show', $student->id) }}" class="action-btn btn-view">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('student.edit', $student->id) }}" class="action-btn btn-edit">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <a href="{{ route('book_issue.create', ['student_id' => $student->id]) }}" class="action-btn btn-issue">
                                        <i class="fas fa-book me-1"></i>Issue Book
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Authors Results -->
            @if(isset($authors) && $authors->count() > 0)
            <div class="search-section">
                <div class="section-header">
                    <h4 class="section-title">
                        <span><i class="fas fa-user-edit me-2"></i>Authors</span>
                        <span class="section-count">{{ $authors->count() }} results</span>
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Books Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($authors as $author)
                            <tr>
                                <td><strong>#{{ $author->id }}</strong></td>
                                <td>
                                    <a href="{{ route('authors.show', $author->id) }}" class="author-link">
                                        {{ $author->name }}
                                    </a>
                                </td>
                                <td>{{ $author->books->count() ?? 0 }} books</td>
                                <td>
                                    <a href="{{ route('authors.show', $author->id) }}" class="action-btn btn-view">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('authors.edit', $author->id) }}" class="action-btn btn-edit">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Categories Results -->
            @if(isset($categories) && $categories->count() > 0)
            <div class="search-section">
                <div class="section-header">
                    <h4 class="section-title">
                        <span><i class="fas fa-tags me-2"></i>Categories</span>
                        <span class="section-count">{{ $categories->count() }} results</span>
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Books Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td><strong>#{{ $category->id }}</strong></td>
                                <td>
                                    <a href="{{ route('categories.show', $category->id) }}" class="category-link">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td>{{ $category->books->count() ?? 0 }} books</td>
                                <td>
                                    <a href="{{ route('categories.show', $category->id) }}" class="action-btn btn-view">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('categories.edit', $category->id) }}" class="action-btn btn-edit">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Publishers Results -->
            @if(isset($publishers) && $publishers->count() > 0)
            <div class="search-section">
                <div class="section-header">
                    <h4 class="section-title">
                        <span><i class="fas fa-building me-2"></i>Publishers</span>
                        <span class="section-count">{{ $publishers->count() }} results</span>
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Books Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($publishers as $publisher)
                            <tr>
                                <td><strong>#{{ $publisher->id }}</strong></td>
                                <td>
                                    <a href="{{ route('publishers.show', $publisher->id) }}" class="publisher-link">
                                        {{ $publisher->name }}
                                    </a>
                                </td>
                                <td>{{ $publisher->books->count() ?? 0 }} books</td>
                                <td>
                                    <a href="{{ route('publishers.show', $publisher->id) }}" class="action-btn btn-view">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('publishers.edit', $publisher->id) }}" class="action-btn btn-edit">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Book Issues Results -->
            @if(isset($bookIssues) && $bookIssues->total() > 0)
            <div class="search-section">
                <div class="section-header">
                    <h4 class="section-title">
                        <span><i class="fas fa-book-open me-2"></i>Book Issues</span>
                        <span class="section-count">{{ $bookIssues->total() }} results</span>
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Book</th>
                                <th>Student</th>
                                <th>Issue Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookIssues as $issue)
                            <tr>
                                <td><strong>#{{ $issue->id }}</strong></td>
                                <td>
                                    <a href="{{ route('books.show', $issue->book->id) }}" class="book-link">
                                        {{ $issue->book->name }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('students.show', $issue->student->id) }}" class="student-link">
                                        {{ $issue->student->name }}
                                    </a>
                                </td>
                                <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                                <td>{{ $issue->return_date->format('M d, Y') }}</td>
                                <td>
                                    @if($issue->issue_status == 'Y')
                                        <span class="status-badge status-returned">Returned</span>
                                    @else
                                        <span class="status-badge status-issued">Issued</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('book_issue.show', $issue->id) }}" class="action-btn btn-view">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    @if($issue->issue_status == 'N')
                                        <a href="{{ route('book_issue.edit', $issue->id) }}" class="action-btn btn-return">
                                            <i class="fas fa-undo me-1"></i>Return
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @endif
    @else
        <div class="search-section">
            <div class="welcome-search">
                <div class="welcome-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Search the Library Database</h3>
                <p>Enter keywords to search across books, authors, students, categories, publishers, and book issues.</p>
            </div>
        </div>
    @endif
</div>
@endsection

.search-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 12px 20px;
    border-radius: 0 8px 8px 0;
    font-weight: 600;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}

.search-section {
    background: white;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.section-header {
    background: #f8f9fa;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e9ecef;
}

.section-title {
    color: #2c3e50;
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.section-count {
    font-size: 0.9rem;
    background: #e9ecef;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    color: #6c757d;
    font-weight: 500;
}

.results-table {
    margin: 0;
    border: none;
}

.results-table thead th {
    background: #f8f9fa;
    border: none;
    border-bottom: 2px solid #e9ecef;
    padding: 1rem 1.5rem;
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.results-table tbody td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: middle;
}

.results-table tbody tr {
    transition: background-color 0.2s ease;
}

.results-table tbody tr:hover {
    background-color: #f8f9fa;
}

.book-link, .author-link, .category-link, .publisher-link {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.book-link:hover, .author-link:hover, .category-link:hover, .publisher-link:hover {
    color: #667eea;
    text-decoration: none;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-available {
    background: #d4edda;
    color: #155724;
}

.status-unavailable {
    background: #f8d7da;
    color: #721c24;
}

.status-returned {
    background: #d4edda;
    color: #155724;
}

.status-issued {
    background: #fff3cd;
    color: #856404;
}

.action-btn {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    margin-right: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn-view {
    background: #e7f3ff;
    color: #0366d6;
    border-color: #c1e3ff;
}

.btn-view:hover {
    background: #0366d6;
    color: white;
    text-decoration: none;
}

.btn-edit {
    background: #fff5e6;
    color: #d97706;
    border-color: #fed7aa;
}

.btn-edit:hover {
    background: #d97706;
    color: white;
    text-decoration: none;
}

.btn-issue {
    background: #f0f9e8;
    color: #16a34a;
    border-color: #bbf7d0;
}

.btn-issue:hover {
    background: #16a34a;
    color: white;
    text-decoration: none;
}

.btn-return {
    background: #fef3c7;
    color: #d97706;
    border-color: #fde68a;
}

.btn-return:hover {
    background: #d97706;
    color: white;
    text-decoration: none;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.no-results-icon {
    font-size: 4rem;
    color: #e9ecef;
    margin-bottom: 1.5rem;
}

.no-results-title {
    color: #6c757d;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.no-results-text {
    color: #adb5bd;
    margin-bottom: 0.5rem;
}

.no-data-text {
    color: #6c757d;
    font-style: italic;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .search-container {
        padding: 1rem 0.5rem;
    }
    
    .search-header {
        padding: 1.5rem;
    }
    
    .search-title {
        font-size: 1.5rem;
    }
    
    .results-table {
        font-size: 0.9rem;
    }
    
    .results-table thead th,
    .results-table tbody td {
        padding: 0.75rem 1rem;
    }
    
    .action-btn {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
    }
}
</style>

<div class="search-container">
    <div class="search-header">
        <h1 class="search-title">Search Results</h1>
        <p class="search-subtitle">Found results for "{{ $query }}"</p>
        
        <form method="GET" action="{{ route('search.results') }}">
            <div class="search-form">
                <input type="text" class="form-control search-input" name="q" value="{{ $query }}" placeholder="Search books, students, authors...">
                <button class="btn search-btn" type="submit">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
        </form>
    </div>

    <!-- Books Results -->
    @if(isset($books) && $books->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-book me-2"></i>Books</span>
                <span class="section-count">{{ $books->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Publisher</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td><strong>#{{ $book->id }}</strong></td>
                        <td>
                            <a href="{{ route('books.show', $book->id) }}" class="book-link">
                                {{ $book->name }}
                            </a>
                        </td>
                        <td>
                            @if($book->author)
                                <a href="{{ route('authors.show', $book->author->id) }}" class="author-link">
                                    {{ $book->author->name }}
                                </a>
                            @else
                                <span class="no-data-text">No author</span>
                            @endif
                        </td>
                        <td>
                            @if($book->category)
                                <a href="{{ route('categories.show', $book->category->id) }}" class="category-link">
                                    {{ $book->category->name }}
                                </a>
                            @else
                                <span class="no-data-text">No category</span>
                            @endif
                        </td>
                        <td>
                            @if($book->publisher)
                                <a href="{{ route('publishers.show', $book->publisher->id) }}" class="publisher-link">
                                    {{ $book->publisher->name }}
                                </a>
                            @else
                                <span class="no-data-text">No publisher</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($book->status) && $book->status == 'Y')
                                <span class="status-badge status-available">Available</span>
                            @else
                                <span class="status-badge status-unavailable">Unavailable</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('books.edit', $book->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            @if(isset($book->status) && $book->status == 'Y')
                                <a href="{{ route('book_issue.create', ['book_id' => $book->id]) }}" class="action-btn btn-issue">
                                    <i class="fas fa-hand-holding me-1"></i>Issue
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Students Results -->
    @if(isset($students) && $students->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-user-graduate me-2"></i>Students</span>
                <span class="section-count">{{ $students->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td><strong>#{{ $student->id }}</strong></td>
                        <td>{{ $student->student_id ?? 'Not Assigned' }}</td>
                        <td>
                            <a href="{{ route('students.show', $student->id) }}" class="book-link">
                                {{ $student->name }}
                            </a>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->phone }}</td>
                        <td>{{ Str::limit($student->address, 30) }}</td>
                        <td>
                            <a href="{{ route('students.show', $student->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('students.edit', $student->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('book_issue.create', ['student_id' => $student->id]) }}" class="action-btn btn-issue">
                                <i class="fas fa-book me-1"></i>Issue Book
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Authors Results -->
    @if(isset($authors) && $authors->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-user-edit me-2"></i>Authors</span>
                <span class="section-count">{{ $authors->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Books Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($authors as $author)
                    <tr>
                        <td><strong>#{{ $author->id }}</strong></td>
                        <td>
                            <a href="{{ route('authors.show', $author->id) }}" class="author-link">
                                {{ $author->name }}
                            </a>
                        </td>
                        <td>{{ $author->books ? $author->books->count() : 0 }} books</td>
                        <td>{{ $author->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('authors.show', $author->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('authors.edit', $author->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Categories Results -->
    @if(isset($categories) && $categories->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-tags me-2"></i>Categories</span>
                <span class="section-count">{{ $categories->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Books Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td><strong>#{{ $category->id }}</strong></td>
                        <td>
                            <a href="{{ route('categories.show', $category->id) }}" class="category-link">
                                {{ $category->name }}
                            </a>
                        </td>
                        <td>{{ $category->books ? $category->books->count() : 0 }} books</td>
                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('categories.show', $category->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('categories.edit', $category->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Publishers Results -->
    @if(isset($publishers) && $publishers->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-building me-2"></i>Publishers</span>
                <span class="section-count">{{ $publishers->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Books Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($publishers as $publisher)
                    <tr>
                        <td><strong>#{{ $publisher->id }}</strong></td>
                        <td>
                            <a href="{{ route('publishers.show', $publisher->id) }}" class="publisher-link">
                                {{ $publisher->name }}
                            </a>
                        </td>
                        <td>{{ $publisher->books ? $publisher->books->count() : 0 }} books</td>
                        <td>{{ $publisher->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('publishers.show', $publisher->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('publishers.edit', $publisher->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Book Issues Results -->
    @if(isset($bookIssues) && $bookIssues->total() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-exchange-alt me-2"></i>Book Issues</span>
                <span class="section-count">{{ $bookIssues->total() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Student</th>
                        <th>Issue Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookIssues as $issue)
                    <tr>
                        <td><strong>#{{ $issue->id }}</strong></td>
                        <td>
                            @if($issue->book)
                                <a href="{{ route('books.show', $issue->book->id) }}" class="book-link">
                                    {{ $issue->book->name }}
                                </a>
                            @else
                                <span class="no-data-text">Book not found</span>
                            @endif
                        </td>
                        <td>
                            @if($issue->student)
                                <a href="{{ route('students.show', $issue->student->id) }}" class="book-link">
                                    {{ $issue->student->name }}
                                </a>
                            @else
                                <span class="no-data-text">Student not found</span>
                            @endif
                        </td>
                        <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                        <td>{{ $issue->return_date->format('M d, Y') }}</td>
                        <td>
                            @if($issue->issue_status == 'Y')
                                <span class="status-badge status-returned">Returned</span>
                            @else
                                <span class="status-badge status-issued">Issued</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('book_issue.show', $issue->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            @if($issue->issue_status == 'N')
                                <a href="{{ route('book_issue.edit', $issue->id) }}" class="action-btn btn-return">
                                    <i class="fas fa-undo me-1"></i>Return
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- No Results Found -->
    @if((!isset($books) || $books->count() == 0) &&
        (!isset($students) || $students->count() == 0) &&
        (!isset($authors) || $authors->count() == 0) &&
        (!isset($categories) || $categories->count() == 0) &&
        (!isset($publishers) || $publishers->count() == 0) &&
        (!isset($bookIssues) || $bookIssues->total() == 0))
    <div class="no-results">
        <div class="no-results-icon">
            <i class="fas fa-search"></i>
        </div>
        <h4 class="no-results-title">No Results Found</h4>
        <p class="no-results-text">We couldn't find anything matching "<strong>{{ $query }}</strong>"</p>
        <p class="no-results-text">Try searching with different keywords or check spelling.</p>
    </div>
    @endif

    <!-- Pagination -->
    @if(isset($books) && $books->hasPages())
    <div class="d-flex justify-content-center">
        {{ $books->appends(['q' => $query])->links() }}
    </div>
    @endif
</div>
@endsection

@section('content')
<style>
.search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.search-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.search-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.search-subtitle {
    opacity: 0.9;
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

.search-form {
    display: flex;
    gap: 0;
    max-width: 600px;
}

.search-input {
    border: none;
    border-radius: 8px 0 0 8px;
    padding: 12px 16px;
    font-size: 1rem;
    box-shadow: none;
    background: white;
    flex: 1;
}

.search-input:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
}

.search-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 12px 20px;
    border-radius: 0 8px 8px 0;
    font-weight: 600;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}

.search-section {
    background: white;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.section-header {
    background: #f8f9fa;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e9ecef;
}

.section-title {
    color: #2c3e50;
    font-size: 1.3rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.section-count {
    font-size: 0.9rem;
    background: #e9ecef;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    color: #6c757d;
    font-weight: 500;
}

.results-table {
    margin: 0;
    border: none;
}

.results-table thead th {
    background: #f8f9fa;
    border: none;
    border-bottom: 2px solid #e9ecef;
    padding: 1rem 1.5rem;
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.results-table tbody td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: middle;
}

.results-table tbody tr {
    transition: background-color 0.2s ease;
}

.results-table tbody tr:hover {
    background-color: #f8f9fa;
}

.book-link, .author-link, .category-link, .publisher-link {
    color: #2c3e50;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
}

.book-link:hover, .author-link:hover, .category-link:hover, .publisher-link:hover {
    color: #667eea;
    text-decoration: none;
}

.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-available {
    background: #d4edda;
    color: #155724;
}

.status-unavailable {
    background: #f8d7da;
    color: #721c24;
}

.status-returned {
    background: #d4edda;
    color: #155724;
}

.status-issued {
    background: #fff3cd;
    color: #856404;
}

.action-btn {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    margin-right: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.btn-view {
    background: #e7f3ff;
    color: #0366d6;
    border-color: #c1e3ff;
}

.btn-view:hover {
    background: #0366d6;
    color: white;
    text-decoration: none;
}

.btn-edit {
    background: #fff5e6;
    color: #d97706;
    border-color: #fed7aa;
}

.btn-edit:hover {
    background: #d97706;
    color: white;
    text-decoration: none;
}

.btn-issue {
    background: #f0f9e8;
    color: #16a34a;
    border-color: #bbf7d0;
}

.btn-issue:hover {
    background: #16a34a;
    color: white;
    text-decoration: none;
}

.btn-return {
    background: #fef3c7;
    color: #d97706;
    border-color: #fde68a;
}

.btn-return:hover {
    background: #d97706;
    color: white;
    text-decoration: none;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.no-results-icon {
    font-size: 4rem;
    color: #e9ecef;
    margin-bottom: 1.5rem;
}

.no-results-title {
    color: #6c757d;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.no-results-text {
    color: #adb5bd;
    margin-bottom: 0.5rem;
}

.no-data-text {
    color: #6c757d;
    font-style: italic;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .search-container {
        padding: 1rem 0.5rem;
    }
    
    .search-header {
        padding: 1.5rem;
    }
    
    .search-title {
        font-size: 1.5rem;
    }
    
    .results-table {
        font-size: 0.9rem;
    }
    
    .results-table thead th,
    .results-table tbody td {
        padding: 0.75rem 1rem;
    }
    
    .action-btn {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
    }
}
</style>

<div class="search-container">
    <div class="search-header">
        <h1 class="search-title">Search Results</h1>
        <p class="search-subtitle">Found results for "{{ $query }}"</p>
        
        <form method="GET" action="{{ route('search.results') }}">
            <div class="search-form">
                <input type="text" class="form-control search-input" name="q" value="{{ $query }}" placeholder="Search books, students, authors...">
                <button class="btn search-btn" type="submit">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </div>
        </form>
    </div>

    <!-- Books Results -->
    @if(isset($books) && $books->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-book me-2"></i>Books</span>
                <span class="section-count">{{ $books->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Publisher</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td><strong>#{{ $book->id }}</strong></td>
                        <td>
                            <a href="{{ route('books.show', $book->id) }}" class="book-link">
                                {{ $book->name }}
                            </a>
                        </td>
                        <td>
                            @if($book->author)
                                <a href="{{ route('authors.show', $book->author->id) }}" class="author-link">
                                    {{ $book->author->name }}
                                </a>
                            @else
                                <span class="no-data-text">No author</span>
                            @endif
                        </td>
                        <td>
                            @if($book->category)
                                <a href="{{ route('categories.show', $book->category->id) }}" class="category-link">
                                    {{ $book->category->name }}
                                </a>
                            @else
                                <span class="no-data-text">No category</span>
                            @endif
                        </td>
                        <td>
                            @if($book->publisher)
                                <a href="{{ route('publishers.show', $book->publisher->id) }}" class="publisher-link">
                                    {{ $book->publisher->name }}
                                </a>
                            @else
                                <span class="no-data-text">No publisher</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($book->status) && $book->status == 'Y')
                                <span class="status-badge status-available">Available</span>
                            @else
                                <span class="status-badge status-unavailable">Unavailable</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('books.edit', $book->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            @if(isset($book->status) && $book->status == 'Y')
                                <a href="{{ route('book_issue.create', ['book_id' => $book->id]) }}" class="action-btn btn-issue">
                                    <i class="fas fa-hand-holding me-1"></i>Issue
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Students Results -->
    @if(isset($students) && $students->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-user-graduate me-2"></i>Students</span>
                <span class="section-count">{{ $students->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td><strong>#{{ $student->id }}</strong></td>
                        <td>{{ $student->student_id ?? 'Not Assigned' }}</td>
                        <td>
                            <a href="{{ route('students.show', $student->id) }}" class="book-link">
                                {{ $student->name }}
                            </a>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->phone }}</td>
                        <td>{{ Str::limit($student->address, 30) }}</td>
                        <td>
                            <a href="{{ route('students.show', $student->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('students.edit', $student->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('book_issue.create', ['student_id' => $student->id]) }}" class="action-btn btn-issue">
                                <i class="fas fa-book me-1"></i>Issue Book
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Authors Results -->
    @if(isset($authors) && $authors->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-user-edit me-2"></i>Authors</span>
                <span class="section-count">{{ $authors->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Books Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($authors as $author)
                    <tr>
                        <td><strong>#{{ $author->id }}</strong></td>
                        <td>
                            <a href="{{ route('authors.show', $author->id) }}" class="author-link">
                                {{ $author->name }}
                            </a>
                        </td>
                        <td>{{ $author->books ? $author->books->count() : 0 }} books</td>
                        <td>{{ $author->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('authors.show', $author->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('authors.edit', $author->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Categories Results -->
    @if(isset($categories) && $categories->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-tags me-2"></i>Categories</span>
                <span class="section-count">{{ $categories->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Books Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td><strong>#{{ $category->id }}</strong></td>
                        <td>
                            <a href="{{ route('categories.show', $category->id) }}" class="category-link">
                                {{ $category->name }}
                            </a>
                        </td>
                        <td>{{ $category->books ? $category->books->count() : 0 }} books</td>
                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('categories.show', $category->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('categories.edit', $category->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Publishers Results -->
    @if(isset($publishers) && $publishers->count() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-building me-2"></i>Publishers</span>
                <span class="section-count">{{ $publishers->count() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Books Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($publishers as $publisher)
                    <tr>
                        <td><strong>#{{ $publisher->id }}</strong></td>
                        <td>
                            <a href="{{ route('publishers.show', $publisher->id) }}" class="publisher-link">
                                {{ $publisher->name }}
                            </a>
                        </td>
                        <td>{{ $publisher->books ? $publisher->books->count() : 0 }} books</td>
                        <td>{{ $publisher->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('publishers.show', $publisher->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            <a href="{{ route('publishers.edit', $publisher->id) }}" class="action-btn btn-edit">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Book Issues Results -->
    @if(isset($bookIssues) && $bookIssues->total() > 0)
    <div class="search-section">
        <div class="section-header">
            <h4 class="section-title">
                <span><i class="fas fa-exchange-alt me-2"></i>Book Issues</span>
                <span class="section-count">{{ $bookIssues->total() }} results</span>
            </h4>
        </div>
        <div class="table-responsive">
            <table class="table results-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Student</th>
                        <th>Issue Date</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookIssues as $issue)
                    <tr>
                        <td><strong>#{{ $issue->id }}</strong></td>
                        <td>
                            @if($issue->book)
                                <a href="{{ route('books.show', $issue->book->id) }}" class="book-link">
                                    {{ $issue->book->name }}
                                </a>
                            @else
                                <span class="no-data-text">Book not found</span>
                            @endif
                        </td>
                        <td>
                            @if($issue->student)
                                <a href="{{ route('students.show', $issue->student->id) }}" class="book-link">
                                    {{ $issue->student->name }}
                                </a>
                            @else
                                <span class="no-data-text">Student not found</span>
                            @endif
                        </td>
                        <td>{{ $issue->issue_date->format('M d, Y') }}</td>
                        <td>{{ $issue->return_date->format('M d, Y') }}</td>
                        <td>
                            @if($issue->issue_status == 'Y')
                                <span class="status-badge status-returned">Returned</span>
                            @else
                                <span class="status-badge status-issued">Issued</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('book_issue.show', $issue->id) }}" class="action-btn btn-view">
                                <i class="fas fa-eye me-1"></i>View
                            </a>
                            @if($issue->issue_status == 'N')
                                <a href="{{ route('book_issue.edit', $issue->id) }}" class="action-btn btn-return">
                                    <i class="fas fa-undo me-1"></i>Return
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- No Results Found -->
    @if((!isset($books) || $books->count() == 0) &&
        (!isset($students) || $students->count() == 0) &&
        (!isset($authors) || $authors->count() == 0) &&
        (!isset($categories) || $categories->count() == 0) &&
        (!isset($publishers) || $publishers->count() == 0) &&
        (!isset($bookIssues) || $bookIssues->total() == 0))
    <div class="no-results">
        <div class="no-results-icon">
            <i class="fas fa-search"></i>
        </div>
        <h4 class="no-results-title">No Results Found</h4>
        <p class="no-results-text">We couldn't find anything matching "<strong>{{ $query }}</strong>"</p>
        <p class="no-results-text">Try searching with different keywords or check spelling.</p>
    </div>
    @endif

    <!-- Pagination -->
    @if(isset($books) && $books->hasPages())
    <div class="d-flex justify-content-center">
        {{ $books->appends(['q' => $query])->links() }}
    </div>
    @endif
</div>
@endsection
