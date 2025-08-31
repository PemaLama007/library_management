@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="detail-card">
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-book me-2"></i>Book Details
                        </h4>
                        <div class="d-flex detail-action-buttons">
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-custom">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('books') }}" class="btn btn-light btn-custom">
                                <i class="fas fa-arrow-left me-1"></i>Back to Books
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table detail-table">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>Book ID</th>
                                <td><strong>{{ $book->id }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-book me-2"></i>Title</th>
                                <td><strong class="detail-title">{{ $book->name }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-user-edit me-2"></i>Author</th>
                                <td>
                                    @if($book->author)
                                        <a href="{{ route('authors.show', $book->author) }}" class="detail-link">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $book->author->name }}
                                        </a>
                                    @else
                                        <span class="detail-no-data">No author assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-tags me-2"></i>Category</th>
                                <td>
                                    @if($book->category)
                                        <a href="{{ route('categories.show', $book->category) }}" class="detail-link">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $book->category->name }}
                                        </a>
                                    @else
                                        <span class="detail-no-data">No category assigned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-building me-2"></i>Publisher</th>
                                <td>
                                    @if($book->publisher)
                                        <a href="{{ route('publishers.show', $book->publisher) }}" class="detail-link">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $book->publisher->name }}
                                        </a>
                                    @else
                                        <span class="detail-no-data">No publisher assigned</span>
                                    @endif
                                </td>
                            </tr>
                            @if(isset($book->isbn) && $book->isbn)
                            <tr>
                                <th><i class="fas fa-barcode me-2"></i>ISBN</th>
                                <td><code class="detail-isbn-code">{{ $book->isbn }}</code></td>
                            </tr>
                            @endif
                            @if(isset($book->description) && $book->description)
                            <tr>
                                <th><i class="fas fa-align-left me-2"></i>Description</th>
                                <td class="detail-description">{{ $book->description }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                <td>
                                    @if(isset($book->status) && $book->status == 'Y')
                                        <span class="detail-status-badge detail-status-available">
                                            <i class="fas fa-check-circle me-1"></i>Available
                                        </span>
                                    @else
                                        <span class="detail-status-badge detail-status-unavailable">
                                            <i class="fas fa-times-circle me-1"></i>Unavailable
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-plus me-2"></i>Added Date</th>
                                <td>{{ $book->created_at->format('F d, Y \a\t h:i A') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-edit me-2"></i>Last Updated</th>
                                <td>{{ $book->updated_at->format('F d, Y \a\t h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Action Buttons Section -->
            <div class="detail-card">
                <div class="card-body text-center detail-quick-actions">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex justify-content-center detail-action-buttons flex-wrap">
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-primary btn-custom">
                            <i class="fas fa-edit me-1"></i>Edit Book
                        </a>
                        <button class="btn btn-success btn-custom" onclick="issueBook({{ $book->id }})">
                            <i class="fas fa-hand-holding me-1"></i>Issue Book
                        </button>
                        <a href="{{ route('books') }}" class="btn btn-info btn-custom">
                            <i class="fas fa-list me-1"></i>View All Books
                        </a>
                        <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-custom">
                                <i class="fas fa-trash me-1"></i>Delete Book
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function issueBook(bookId) {
    alert('Book issue functionality would be implemented here. Book ID: ' + bookId);
    // This would typically redirect to book issue page or open a modal
}
</script>
@endsection
