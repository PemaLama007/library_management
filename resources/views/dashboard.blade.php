@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <!-- Quick Stats Cards at the top -->
            <div class="row mb-4">
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card card-authors">
                        <div class="card-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="card-content">
                            <h4 class="stats-number">{{ $authors }}</h4>
                            <p class="stats-label">Authors</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card card-publishers">
                        <div class="card-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="card-content">
                            <h4 class="stats-number">{{ $publishers }}</h4>
                            <p class="stats-label">Publishers</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card card-categories">
                        <div class="card-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="card-content">
                            <h4 class="stats-number">{{ $categories }}</h4>
                            <p class="stats-label">Categories</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card card-books">
                        <div class="card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="card-content">
                            <h4 class="stats-number">{{ $books }}</h4>
                            <p class="stats-label">Books</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card card-students">
                        <div class="card-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="card-content">
                            <h4 class="stats-number">{{ $students }}</h4>
                            <p class="stats-label">Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card card-issued">
                        <div class="card-icon">
                            <i class="fas fa-book-reader"></i>
                        </div>
                        <div class="card-content">
                            <h4 class="stats-number">{{ $issued_books }}</h4>
                            <p class="stats-label">Issued</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="row">
                <!-- Left Column: Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h5><i class="fas fa-chart-pie"></i> Book Categories Distribution</h5>
                        </div>
                        <div class="chart-body">
                            <canvas id="categoriesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Dashboard Header and Quick Actions -->
                <div class="col-lg-6 mb-4">
                    <!-- Dashboard Header -->
                    <div class="dashboard-header mb-4">
                        <h2 class="admin-heading text-white"><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
                        <p class="text-white">Welcome back! Here's what's happening in your library today.</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions-card">
                        <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                        <div class="action-buttons">
                            <a href="{{ route('book.create') }}" class="action-btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Book
                            </a>
                            <a href="{{ route('student.create') }}" class="action-btn btn-success btn-sm">
                                <i class="fas fa-user-plus"></i> Register Student
                            </a>
                            <a href="{{ route('book_issue.create') }}" class="action-btn btn-warning btn-sm">
                                <i class="fas fa-book-open"></i> Issue Book
                            </a>
                            <a href="{{ route('reports') }}" class="action-btn btn-info btn-sm">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Book Categories Distribution Chart using real data
        const ctx = document.getElementById('categoriesChart').getContext('2d');
        
        // Get category data from backend
        const categoryData = @json($category_data);
        const categoryLabels = categoryData.map(item => item.name);
        const categoryValues = categoryData.map(item => item.book_count);
        
        // Generate colors dynamically based on number of categories
        const colors = [
            '#1B3F63', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
            '#17a2b8', '#fd7e14', '#e83e8c', '#6c757d', '#007bff'
        ];
        
        const categoriesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels.length > 0 ? categoryLabels : ['No Categories'],
                datasets: [{
                    data: categoryValues.length > 0 ? categoryValues : [1],
                    backgroundColor: colors.slice(0, Math.max(categoryLabels.length, 1))
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} books (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
