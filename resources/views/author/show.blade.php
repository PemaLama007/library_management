@extends('layouts.app')

@section('content')
    <style>
        .detail-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .detail-card .card-body {
            background: white;
        }

        .detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }

        .detail-table {
            margin: 0;
            background: white;
        }

        .detail-table td,
        .detail-table th {
            border: none;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 20px;
            vertical-align: middle;
        }

        .detail-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 600;
            color: #495057;
            width: 25%;
        }

        .detail-table td {
            color: #6c757d;
            font-weight: 500;
        }

        .detail-table tr:last-child td,
        .detail-table tr:last-child th {
            border-bottom: none;
        }

        .detail-table tr:hover {
            background: #f8f9fa;
        }

        .detail-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .detail-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .book-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .book-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .action-buttons {
            gap: 10px;
        }

        .btn-custom {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="detail-card">
                    <div class="detail-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i>Author Details
                            </h4>
                            <div class="d-flex action-buttons">
                                <a href="{{ route('authors.edit', $author) }}" class="btn btn-warning btn-custom">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <a href="{{ route('authors') }}" class="btn btn-light btn-custom">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Authors
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table detail-table">
                            <tbody>
                                <tr>
                                    <th><i class="fas fa-hashtag me-2"></i>Author ID</th>
                                    <td><strong>{{ $author->id }}</strong></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Author Name</th>
                                    <td><strong style="color: #495057; font-size: 16px;">{{ $author->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-plus me-2"></i>Added Date</th>
                                    <td>{{ $author->created_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-edit me-2"></i>Last Updated</th>
                                    <td>{{ $author->updated_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Quick Actions -->
                <div class="detail-card">
                    <div class="card-body text-center">
                        <h6 class="mb-3" style="color: #495057;">Quick Actions</h6>
                        <div class="d-flex flex-column gap-2">
                            <a href="{{ route('authors.edit', $author) }}" class="btn btn-primary btn-custom my-2">
                                <i class="fas fa-edit me-1"></i>Edit Author
                            </a>
                            <a href="{{ route('authors') }}" class="btn btn-info btn-custom">
                                <i class="fas fa-list me-1"></i>View All Authors
                            </a>
                            <form action="{{ route('authors.destroy', $author->id) }}" method="POST" class="d-inline my-2"
                                onsubmit="return confirm('Are you sure you want to delete this author?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-custom w-100">
                                    <i class="fas fa-trash me-1"></i>Delete Author
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </tr>
        <tr>
            <td><strong>Created:</strong></td>
            <td>{{ $author->created_at->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td><strong>Updated:</strong></td>
            <td>{{ $author->updated_at->format('M d, Y') }}</td>
        </tr>
        <div class="row mt-5">
        <div class="col-md-12">
            <h5>Books by this Author</h5>
            @if ($author->books && $author->books->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>ISBN</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($author->books as $book)
                                <tr>
                                    <td>{{ $book->name }}</td>
                                    <td>{{ $book->isbn }}</td>
                                    <td>
                                        @if ($book->status == 'Y')
                                            <span class="badge badge-success">Available</span>
                                        @else
                                            <span class="badge badge-danger">Issued</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No books found for this author.</p>
            @endif
        </div>
    </div>
    </div>
    
@endsection
