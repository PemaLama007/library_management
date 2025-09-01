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
    
    .detail-table td, .detail-table th {
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
                            <i class="fas fa-user me-2"></i>Student Details
                        </h4>
                        <div class="d-flex action-buttons">
                            <a href="{{ route('student.edit', $student) }}" class="btn btn-warning btn-custom">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <a href="{{ route('students') }}" class="btn btn-light btn-custom">
                                <i class="fas fa-arrow-left me-1"></i>Back to Students
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table detail-table">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>Student ID</th>
                                <td><strong>{{ $student->id }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-id-card me-2"></i>Student Number</th>
                                <td><strong style="color: #495057;">{{ $student->student_id ?? 'Not assigned' }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-user me-2"></i>Full Name</th>
                                <td><strong style="color: #495057; font-size: 16px;">{{ $student->name }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-envelope me-2"></i>Email</th>
                                <td>
                                    @if($student->email)
                                        <a href="mailto:{{ $student->email }}" class="detail-link">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $student->email }}
                                        </a>
                                    @else
                                        <span style="color: #6c757d; font-style: italic;">No email provided</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-phone me-2"></i>Phone</th>
                                <td>
                                    @if($student->phone)
                                        <a href="tel:{{ $student->phone }}" class="detail-link">
                                            <i class="fas fa-external-link-alt me-1"></i>{{ $student->phone }}
                                        </a>
                                    @else
                                        <span style="color: #6c757d; font-style: italic;">No phone provided</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-map-marker-alt me-2"></i>Address</th>
                                <td style="line-height: 1.6;">{{ $student->address ?? 'No address provided' }}</td>
                            </tr>
                            @if(isset($student->library_card_number))
                            <tr>
                                <th><i class="fas fa-id-badge me-2"></i>Library Card</th>
                                <td><code style="background: #f8f9fa; padding: 4px 8px; border-radius: 4px;">{{ $student->library_card_number ?? 'Not assigned' }}</code></td>
                            </tr>
                            @endif
                            <tr>
                                <th><i class="fas fa-calendar-plus me-2"></i>Registration Date</th>
                                <td>{{ $student->created_at->format('F d, Y \a\t h:i A') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-calendar-edit me-2"></i>Last Updated</th>
                                <td>{{ $student->updated_at->format('F d, Y \a\t h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mt-2">
            <!-- Student Statistics -->
            <div class="stats-card">
                <h6 class="mb-3">Library Statistics</h6>
                @php
                    $currentIssues = $student->bookIssues ? $student->bookIssues->where('return_date', null)->count() : 0;
                    $totalIssues = $student->bookIssues ? $student->bookIssues->count() : 0;
                @endphp
                <div class="row">
                    <div class="col-6">
                        <div class="stats-number">{{ $currentIssues }}</div>
                        <small>Current Issues</small>
                    </div>
                    <div class="col-6">
                        <div class="stats-number">{{ $totalIssues }}</div>
                        <small>Total Issues</small>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="detail-card">
                <div class="card-body text-center" style="padding: 30px;">
                    <h6 class="mb-3 text-white">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.edit', $student) }}" class="btn btn-primary btn-custom my-2">
                            <i class="fas fa-edit me-1"></i>Edit Student
                        </a>
                        <button class="btn btn-success btn-custom" onclick="issueBook({{ $student->id }})">
                            <i class="fas fa-book me-1"></i>Issue Book
                        </button>
                        <form action="{{ route('student.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this student?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-custom w-100">
                                <i class="fas fa-trash me-1"></i>Delete Student
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    <!-- Current Book Issues -->
                    @if($student->bookIssues && $student->bookIssues->where('return_date', null)->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0 ">Current Book Issues</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Book</th>
                                                    <th>Issue Date</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($student->bookIssues->where('return_date', null) as $issue)
                                                <tr>
                                                    <td>
                                                        @if($issue->book)
                                                            <a href="{{ route('books.show', $issue->book) }}">
                                                                {{ $issue->book->name }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Unknown Book</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $issue->issue_date ? $issue->issue_date->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $issue->due_date ? $issue->due_date->format('M d, Y') : 'No due date' }}</td>
                                                    <td>
                                                        @if($issue->due_date && $issue->due_date->isPast())
                                                            <span class="badge badge-danger">Overdue</span>
                                                        @else
                                                            <span class="badge badge-success">Active</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info" onclick="returnBook({{ $issue->id }})">
                                                            <i class="fas fa-undo"></i> Return
                                                        </button>
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

                    <!-- Book Issue History -->
                    @if($student->bookIssues && $student->bookIssues->where('return_date', '!=', null)->count() > 0)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Book Issue History</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive text-decoration-none">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr class="text-white">
                                                    <th>Book</th>
                                                    <th>Issue Date</th>
                                                    <th>Return Date</th>
                                                    <th>Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-white">
                                                @foreach($student->bookIssues->where('return_date', '!=', null)->take(10) as $issue)
                                                <tr>
                                                    <td>
                                                        @if($issue->book)
                                                            <a href="{{ route('books.show', $issue->book) }}">
                                                                {{ $issue->book->name }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Unknown Book</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $issue->issue_date ? $issue->issue_date->format('M d, Y') : 'N/A' }}</td>
                                                    <td>{{ $issue->return_date ? $issue->return_date->format('M d, Y') : 'N/A' }}</td>
                                                    <td>
                                                        @if($issue->issue_date && $issue->return_date)
                                                            {{ $issue->issue_date->diffInDays($issue->return_date) }} days
                                                        @else
                                                            N/A
                                                        @endif
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

<script>
function issueBook(studentId) {
    // This would typically open a modal or redirect to book issue page
    alert('Book issue functionality would be implemented here. Student ID: ' + studentId);
}

function returnBook(issueId) {
    // This would typically handle book return
    if(confirm('Are you sure you want to mark this book as returned?')) {
        alert('Book return functionality would be implemented here. Issue ID: ' + issueId);
    }
}
</script>
@endsection
