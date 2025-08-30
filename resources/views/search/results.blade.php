@extends('layouts.app')

@section('content')
<div id="content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h3>Search Results</h3>
                                <p class="text-muted">Search query: "{{ $query }}"</p>
                            </div>
                            <div class="col-md-4">
                                <form action="{{ route('search') }}" method="GET" class="form-inline float-right">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="q" value="{{ $query }}" placeholder="Search again...">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Books Results -->
                        @if($books->count() > 0)
                        <div class="search-section mb-4">
                            <h4 class="section-title">
                                <i class="fas fa-book text-primary"></i> Books ({{ $books->count() }} results)
                            </h4>
                            <div class="row">
                                @foreach($books as $book)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $book->name }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Author:</strong> {{ $book->auther->name ?? 'N/A' }}<br>
                                                    <strong>Category:</strong> {{ $book->category->name ?? 'N/A' }}<br>
                                                    <strong>Publisher:</strong> {{ $book->publisher->name ?? 'N/A' }}<br>
                                                    @if($book->total_copies)
                                                        <strong>Available:</strong> {{ $book->available_copies }}/{{ $book->total_copies }}
                                                    @endif
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('books.show', $book->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('books.edit', $book->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                @if($book->isAvailable())
                                                <a href="{{ route('book_issue.create', ['book_id' => $book->id]) }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-hand-holding"></i> Issue
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Students Results -->
                        @if($students->count() > 0)
                        <div class="search-section mb-4">
                            <h4 class="section-title">
                                <i class="fas fa-user-graduate text-success"></i> Students ({{ $students->count() }} results)
                            </h4>
                            <div class="row">
                                @foreach($students as $student)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $student->name }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Student ID:</strong> {{ $student->student_id ?? 'Not Assigned' }}<br>
                                                    <strong>Email:</strong> {{ $student->email }}<br>
                                                    <strong>Phone:</strong> {{ $student->phone }}<br>
                                                    <strong>Address:</strong> {{ $student->address }}
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="{{ route('book_issue.create', ['student_id' => $student->id]) }}" class="btn btn-outline-success btn-sm">
                                                    <i class="fas fa-book"></i> Issue Book
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Authors Results -->
                        @if($authors->count() > 0)
                        <div class="search-section mb-4">
                            <h4 class="section-title">
                                <i class="fas fa-user-edit text-info"></i> Authors ({{ $authors->count() }} results)
                            </h4>
                            <div class="row">
                                @foreach($authors as $author)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $author->name }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Books:</strong> {{ $author->books->count() }} books
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('authors.show', $author->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('authors.edit', $author->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Categories Results -->
                        @if($categories->count() > 0)
                        <div class="search-section mb-4">
                            <h4 class="section-title">
                                <i class="fas fa-tags text-warning"></i> Categories ({{ $categories->count() }} results)
                            </h4>
                            <div class="row">
                                @foreach($categories as $category)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $category->name }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Books:</strong> {{ $category->books->count() }} books
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Publishers Results -->
                        @if($publishers->count() > 0)
                        <div class="search-section mb-4">
                            <h4 class="section-title">
                                <i class="fas fa-building text-secondary"></i> Publishers ({{ $publishers->count() }} results)
                            </h4>
                            <div class="row">
                                @foreach($publishers as $publisher)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $publisher->name }}</h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <strong>Books:</strong> {{ $publisher->books->count() }} books
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('publishers.show', $publisher->id) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('publishers.edit', $publisher->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Book Issues Results -->
                        @if($bookIssues->count() > 0)
                        <div class="search-section mb-4">
                            <h4 class="section-title">
                                <i class="fas fa-exchange-alt text-danger"></i> Book Issues ({{ $bookIssues->count() }} results)
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Book</th>
                                            <th>Issue Date</th>
                                            <th>Return Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bookIssues as $issue)
                                        <tr>
                                            <td>{{ $issue->student->name }}</td>
                                            <td>{{ $issue->book->name }}</td>
                                            <td>{{ date('M d, Y', strtotime($issue->issue_date)) }}</td>
                                            <td>{{ date('M d, Y', strtotime($issue->return_date)) }}</td>
                                            <td>
                                                @if($issue->issue_status == 'Y')
                                                    <span class="badge badge-success">Returned</span>
                                                @else
                                                    @if(strtotime($issue->return_date) < time())
                                                        <span class="badge badge-danger">Overdue</span>
                                                    @else
                                                        <span class="badge badge-warning">Issued</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('book_issued.show', $issue->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- No Results -->
                        @if($books->count() == 0 && $students->count() == 0 && $authors->count() == 0 &&
                            $categories->count() == 0 && $publishers->count() == 0 && $bookIssues->count() == 0)
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5>No results found</h5>
                            <p class="text-muted">Try searching with different keywords or check the spelling.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-home"></i> Back to Dashboard
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.search-section {
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
}

.section-title {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #1B3F63;
    color: #1B3F63;
}

.card {
    border: 1px solid #ddd;
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection
