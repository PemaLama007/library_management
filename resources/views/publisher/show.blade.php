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

        .stats-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
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
            <div class="col-md-8">
                <div class="detail-card">
                    <div class="detail-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-building me-2"></i>Publisher Details
                            </h4>
                            <div class="d-flex action-buttons">
                                <a href="{{ route('publishers.edit', $publisher) }}" class="btn btn-warning btn-custom">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <a href="{{ route('publishers') }}" class="btn btn-light btn-custom">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Publishers
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table detail-table">
                            <tbody>
                                <tr>
                                    <th><i class="fas fa-hashtag me-2"></i>Publisher ID</th>
                                    <td><strong>{{ $publisher->id }}</strong></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-building me-2"></i>Publisher Name</th>
                                    <td><strong style="color: #495057; font-size: 16px;">{{ $publisher->name }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-book me-2"></i>Total Books</th>
                                    <td>
                                        @if ($publisher->books)
                                            <span class="badge badge-info"
                                                style="background: #17a2b8; color: white; padding: 5px 10px; border-radius: 15px;">{{ $publisher->books->count() }}
                                                books</span>
                                        @else
                                            <span class="badge badge-secondary"
                                                style="background: #6c757d; color: white; padding: 5px 10px; border-radius: 15px;">0
                                                books</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-plus me-2"></i>Added Date</th>
                                    <td>{{ $publisher->created_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-calendar-edit me-2"></i>Last Updated</th>
                                    <td>{{ $publisher->updated_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mt-2">
                <!-- Statistics Card -->
                <div class="stats-card">
                    <h6 class="mb-3">Publishing Statistics</h6>
                    <div class="stats-number">{{ $publisher->books ? $publisher->books->count() : 0 }}</div>
                    <small>Total Books Published</small>
                </div>

                <!-- Action Buttons -->
                <div class="detail-card">
                    <div class="card-body text-center" style="padding: 30px;">
                        <h6 class="mb-3 text-white">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('publishers.edit', $publisher) }}" class="btn btn-primary btn-custom my-2">
                                <i class="fas fa-edit me-1"></i>Edit Publisher
                            </a>
                            <a href="{{ route('publishers') }}" class="btn btn-info btn-custom">
                                <i class="fas fa-list me-1"></i>View All Publishers
                            </a>
                            <form action="{{ route('publishers.destroy', $publisher->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this publisher?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-custom w-100 my-2">
                                    <i class="fas fa-trash me-1"></i>Delete Publisher
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($publisher->books && $publisher->books->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Books by this Publisher</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="text-white">
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Category</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-white">
                                        @foreach ($publisher->books as $book)
                                            <tr>
                                                <td>{{ $book->id }}</td>
                                                <td>{{ $book->name }}</td>
                                                <td>{{ $book->author->name ?? 'N/A' }}</td>
                                                <td>{{ $book->category->name ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($book->status == 'Y')
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    </div>
    </div>
    </div>
    </div>
@endsection
