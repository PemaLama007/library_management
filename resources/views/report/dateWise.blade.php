@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container-fluid">
            <!-- Report Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="reports-header">
                        <h2 class="admin-heading"><i class="fas fa-calendar-day"></i> Date Wise Book Issue Report</h2>
                        <p class="text-muted">Generate detailed reports for specific dates and analyze daily library activity</p>
                    </div>
                </div>
            </div>

            <!-- Search Form Section -->
            <div class="row mb-4">
                <div class="col-lg-6 offset-lg-3">
                    <div class="search-card">
                        <h4><i class="fas fa-search"></i> Select Report Date</h4>
                        <form class="date-search-form" action="{{ route('reports.date_wise_generate') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="date" class="form-label">Choose Date:</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                @error('date')
                                    <div class="alert alert-danger mt-2" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-search" name="search_date">
                                <i class="fas fa-search"></i> Generate Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if ($books)
                <!-- Report Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card card-primary">
                            <div class="summary-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="summary-content">
                                <h3>{{ count($books) }}</h3>
                                <p>Total Issues</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card card-success">
                            <div class="summary-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="summary-content">
                                <h3>{{ $books->unique('student_id')->count() }}</h3>
                                <p>Unique Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card card-warning">
                            <div class="summary-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="summary-content">
                                <h3>{{ $books->map(function($book) { return $book->book->category; })->unique()->count() }}</h3>
                                <p>Categories</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card card-info">
                            <div class="summary-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="summary-content">
                                <h3>{{ $books->first()->issue_date->format('M d') }}</h3>
                                <p>Report Date</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="export-actions">
                            <h5><i class="fas fa-download"></i> Export Options</h5>
                            <div class="export-buttons">
                                <button class="btn btn-outline-danger" onclick="exportPDF()">
                                    <i class="fas fa-file-pdf"></i> Export PDF
                                </button>
                                <button class="btn btn-outline-success" onclick="exportExcel()">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </button>
                                <button class="btn btn-outline-primary" onclick="printReport()">
                                    <i class="fas fa-print"></i> Print Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="report-table-card">
                            <div class="table-header">
                                <h4><i class="fas fa-table"></i> Book Issues Details</h4>
                                <div class="table-controls">
                                    <input type="text" class="form-control" id="tableSearch" placeholder="Search in table...">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-enhanced" id="reportsTable">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> S.No</th>
                                            <th><i class="fas fa-user"></i> Student Name</th>
                                            <th><i class="fas fa-book"></i> Book Name</th>
                                            <th><i class="fas fa-user-edit"></i> Author</th>
                                            <th><i class="fas fa-tags"></i> Category</th>
                                            <th><i class="fas fa-phone"></i> Phone</th>
                                            <th><i class="fas fa-envelope"></i> Email</th>
                                            <th><i class="fas fa-calendar"></i> Issue Date</th>
                                            <th><i class="fas fa-clock"></i> Due Date</th>
                                            <th><i class="fas fa-info-circle"></i> Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($books as $index => $book)
                                            <tr>
                                                <td><span class="badge badge-light">{{ $index + 1 }}</span></td>
                                                <td>
                                                    <div class="student-info">
                                                        <strong>{{ $book->student->name }}</strong>
                                                        <small class="text-muted d-block">ID: {{ $book->student->id }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="book-info">
                                                        <strong>{{ $book->book->name }}</strong>
                                                        <small class="text-muted d-block">ISBN: {{ $book->book->isbn ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>{{ $book->book->auther->name ?? 'Unknown' }}</td>
                                                <td>
                                                    <span class="badge badge-secondary">{{ $book->book->category->name ?? 'Uncategorized' }}</span>
                                                </td>
                                                <td>
                                                    <a href="tel:{{ $book->student->phone }}" class="text-decoration-none">
                                                        <i class="fas fa-phone text-success"></i> {{ $book->student->phone }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="mailto:{{ $book->student->email }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope text-primary"></i> {{ $book->student->email }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="date-badge">
                                                        <i class="fas fa-calendar-check"></i>
                                                        {{ $book->issue_date->format('d M, Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $dueDate = $book->issue_date->addDays(14); // Assuming 14 days loan period
                                                        $isOverdue = $dueDate < now();
                                                    @endphp
                                                    <span class="date-badge {{ $isOverdue ? 'text-danger' : 'text-success' }}">
                                                        <i class="fas fa-calendar-times"></i>
                                                        {{ $dueDate->format('d M, Y') }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($book->issue_status === 'returned')
                                                        <span class="status-badge status-returned">
                                                            <i class="fas fa-check-circle"></i> Returned
                                                        </span>
                                                    @elseif($isOverdue)
                                                        <span class="status-badge status-overdue">
                                                            <i class="fas fa-exclamation-triangle"></i> Overdue
                                                        </span>
                                                    @else
                                                        <span class="status-badge status-active">
                                                            <i class="fas fa-book-reader"></i> Active
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5">
                                                    <div class="no-data">
                                                        <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                                        <h5 class="mt-3 text-muted">No Records Found!</h5>
                                                        <p class="text-muted">No book issues were recorded for the selected date.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Table search functionality
        document.getElementById('tableSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#reportsTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Export functions
        function exportPDF() {
            alert('PDF export functionality will be implemented. This requires backend implementation.');
        }

        function exportExcel() {
            alert('Excel export functionality will be implemented. This requires backend implementation.');
        }

        function printReport() {
            window.print();
        }

        // Auto-focus on date input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('date').focus();
        });
    </script>

    <!-- Print Styles -->
    <style>
        @media print {
            .btn, .export-actions, .table-controls, .search-card {
                display: none !important;
            }
            
            .report-table-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            .table-enhanced {
                font-size: 12px;
            }
        }
    </style>
@endsection
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
        @endif
    </div>
    </div>
@endsection
