@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="detail-card">
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-book-open me-2"></i>Book Issue Details
                        </h4>
                        <div class="d-flex detail-action-buttons">
                            @if($bookIssue->issue_status == 'N')
                                <a href="{{ route('book_issue.edit', $bookIssue->id) }}" class="btn btn-warning btn-custom">
                                    <i class="fas fa-undo me-1"></i>Return Book
                                </a>
                            @endif
                            <a href="{{ route('book_issue') }}" class="btn btn-light btn-custom">
                                <i class="fas fa-arrow-left me-1"></i>Back to Issues
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table detail-table">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>Issue ID</th>
                                <td><strong>{{ $bookIssue->id }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-book me-2"></i>Book Title</th>
                                <td>
                                    <strong class="detail-title">{{ $bookIssue->book->name }}</strong>
                                    @if($bookIssue->book->author)
                                        <br><small class="text-muted">by {{ $bookIssue->book->author->name }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-user-graduate me-2"></i>Student</th>
                                <td>
                                    <a href="{{ route('students.show', $bookIssue->student) }}" class="detail-link">
                                        <i class="fas fa-external-link-alt me-1"></i>{{ $bookIssue->student->name }}
                                    </a>
                                    @if($bookIssue->student->email)
                                        <br><small class="text-muted">{{ $bookIssue->student->email }}</small>
                                    @endif
                                    @if($bookIssue->student->phone)
                                        <br><small class="text-muted">{{ $bookIssue->student->phone }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-plus me-2"></i>Issue Date</th>
                                <td>{{ $bookIssue->issue_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-check me-2"></i>Return Date</th>
                                <td>{{ $bookIssue->return_date->format('F d, Y') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                <td>
                                    @if($bookIssue->issue_status == 'Y')
                                        <span class="detail-status-badge detail-status-available">
                                            <i class="fas fa-check-circle me-1"></i>Returned
                                        </span>
                                    @else
                                        <span class="detail-status-badge detail-status-unavailable">
                                            <i class="fas fa-clock me-1"></i>Issued
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @if($bookIssue->issue_status == 'N')
                                <tr>
                                    <th><i class="fas fa-exclamation-triangle me-2"></i>Days Status</th>
                                    <td>
                                        @php
                                            $currentDate = date_create(date('Y-m-d'));
                                            $returnDate = date_create($bookIssue->return_date->format('Y-m-d'));
                                            $diff = date_diff($returnDate, $currentDate);
                                            $daysDiff = $diff->format('%a');
                                            $isOverdue = $currentDate > $returnDate;
                                        @endphp
                                        
                                        @if($isOverdue)
                                            <span class="detail-status-badge detail-status-unavailable">
                                                <i class="fas fa-exclamation-triangle me-1"></i>{{ $daysDiff }} day(s) overdue
                                            </span>
                                        @else
                                            <span class="detail-status-badge detail-status-available">
                                                <i class="fas fa-check me-1"></i>{{ $daysDiff }} day(s) remaining
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            @if($fine > 0)
                                <tr>
                                    <th><i class="fas fa-dollar-sign me-2"></i>Fine Amount</th>
                                    <td>
                                        <span class="detail-status-badge detail-status-unavailable">
                                            <i class="fas fa-exclamation-circle me-1"></i>${{ number_format($fine, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                            @if($bookIssue->book->category)
                                <tr>
                                    <th><i class="fas fa-tags me-2"></i>Category</th>
                                    <td>
                                        <a href="{{ route('categories.show', $bookIssue->book->category) }}" class="detail-link">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $bookIssue->book->category->name }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                            @if($bookIssue->book->publisher)
                                <tr>
                                    <th><i class="fas fa-building me-2"></i>Publisher</th>
                                    <td>
                                        <a href="{{ route('publishers.show', $bookIssue->book->publisher) }}" class="detail-link">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $bookIssue->book->publisher->name }}
                                        </a>
                                    </td>
                                </tr>
                            @endif
                            @if($bookIssue->book->isbn)
                                <tr>
                                    <th><i class="fas fa-barcode me-2"></i>ISBN</th>
                                    <td><code class="detail-isbn-code">{{ $bookIssue->book->isbn }}</code></td>
                                </tr>
                            @endif
                            <tr>
                                <th><i class="fas fa-calendar-plus me-2"></i>Record Created</th>
                                <td>{{ $bookIssue->created_at->format('F d, Y \a\t h:i A') }}</td>
                            </tr>
                            @if($bookIssue->updated_at != $bookIssue->created_at)
                                <tr>
                                    <th><i class="fas fa-calendar-edit me-2"></i>Last Updated</th>
                                    <td>{{ $bookIssue->updated_at->format('F d, Y \a\t h:i A') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions Section -->
            @if($bookIssue->issue_status == 'N')
                <div class="detail-card">
                    <div class="card-body text-center detail-quick-actions">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="d-flex justify-content-center detail-action-buttons flex-wrap">
                            <a href="{{ route('book_issue.edit', $bookIssue->id) }}" class="btn btn-success btn-custom">
                                <i class="fas fa-undo me-1"></i>Return Book
                            </a>
                            <a href="{{ route('books.show', $bookIssue->book) }}" class="btn btn-info btn-custom">
                                <i class="fas fa-book me-1"></i>View Book Details
                            </a>
                            <a href="{{ route('students.show', $bookIssue->student) }}" class="btn btn-primary btn-custom">
                                <i class="fas fa-user me-1"></i>View Student Details
                            </a>
                            <a href="{{ route('book_issue') }}" class="btn btn-secondary btn-custom">
                                <i class="fas fa-list me-1"></i>All Book Issues
                            </a>
                        </div>
                        @if($fine > 0)
                            <div class="mt-3">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Overdue Fine:</strong> This book is overdue. Fine amount: <strong>${{ number_format($fine, 2) }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="detail-card">
                    <div class="card-body text-center detail-quick-actions">
                        <h5 class="mb-3">Book Returned</h5>
                        <div class="d-flex justify-content-center detail-action-buttons flex-wrap">
                            <a href="{{ route('books.show', $bookIssue->book) }}" class="btn btn-info btn-custom">
                                <i class="fas fa-book me-1"></i>View Book Details
                            </a>
                            <a href="{{ route('students.show', $bookIssue->student) }}" class="btn btn-primary btn-custom">
                                <i class="fas fa-user me-1"></i>View Student Details
                            </a>
                            <a href="{{ route('book_issue') }}" class="btn btn-secondary btn-custom">
                                <i class="fas fa-list me-1"></i>All Book Issues
                            </a>
                        </div>
                        <div class="mt-3">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Book Returned:</strong> This book has been successfully returned.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
