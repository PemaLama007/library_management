@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <!-- Clustering Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="clustering-header">
                        <h2 class="admin-heading text-white"><i class="fas fa-brain"></i> K-Means Clustering Analysis</h2>
                    </div>
                </div>
            </div>

            <!-- Clustering Options -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="clustering-card">
                        <div class="card-header-custom bg-primary">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Student Behavior Analysis</h5>
                        </div>
                        <div class="card-body-custom">
                            <p class="card-description">Cluster students by borrowing patterns, overdue rates, and reading preferences</p>
                            <form id="studentClusteringForm" class="clustering-form">
                                <div class="form-group">
                                    <label for="studentK">Number of Clusters (K):</label>
                                    <select class="form-control custom-select" id="studentK" name="k">
                                        <option value="2">2 Clusters</option>
                                        <option value="3" selected>3 Clusters</option>
                                        <option value="4">4 Clusters</option>
                                        <option value="5">5 Clusters</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-play"></i> Analyze Students
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="clustering-card">
                        <div class="card-header-custom bg-success">
                            <div class="card-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <h5>Book Usage Analysis</h5>
                        </div>
                        <div class="card-body-custom">
                            <p class="card-description">Cluster books by popularity, availability, and borrowing patterns</p>
                            <form id="bookClusteringForm" class="clustering-form">
                                <div class="form-group">
                                    <label for="bookK">Number of Clusters (K):</label>
                                    <select class="form-control custom-select" id="bookK" name="k">
                                        <option value="2">2 Clusters</option>
                                        <option value="3">3 Clusters</option>
                                        <option value="4" selected>4 Clusters</option>
                                        <option value="5">5 Clusters</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-play"></i> Analyze Books
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="clustering-card">
                        <div class="card-header-custom bg-warning">
                            <div class="card-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5>Borrowing Pattern Analysis</h5>
                        </div>
                        <div class="card-body-custom">
                            <p class="card-description">Cluster borrowing patterns by time, frequency, and seasonal trends</p>
                            <form id="borrowingClusteringForm" class="clustering-form">
                                <div class="form-group">
                                    <label for="borrowingK">Number of Clusters (K):</label>
                                    <select class="form-control custom-select" id="borrowingK" name="k">
                                        <option value="2">2 Clusters</option>
                                        <option value="3" selected>3 Clusters</option>
                                        <option value="4">4 Clusters</option>
                                        <option value="5">5 Clusters</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-play"></i> Analyze Patterns
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="quick-actions-card">
                        <h5 class="quick-actions-title"><i class="fas fa-bolt"></i> Quick Actions</h5>
                        <div class="quick-actions-buttons">
                            <a href="{{ route('reports.clustering.comprehensive') }}" class="btn btn-info btn-lg">
                                <i class="fas fa-chart-line"></i> Comprehensive Report
                            </a>
                            <button class="btn btn-secondary btn-lg" onclick="exportComprehensiveData()">
                                <i class="fas fa-download"></i> Export All Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div id="clusteringResults" class="row" style="display: none;">
                <div class="col-12">
                    <div class="results-header">
                        <h4><i class="fas fa-chart-bar"></i> Clustering Results</h4>
                        <button class="btn btn-outline-secondary btn-sm" onclick="clearResults()">
                            <i class="fas fa-times"></i> Clear Results
                        </button>
                    </div>
                    <div id="resultsContent"></div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="text-center" style="display: none;">
                <div class="loading-container">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="loading-text">Analyzing data with K-Means algorithm...</p>
                </div>
            </div>

            <!-- Back to Reports -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="{{ route('reports') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Student Clustering
        document.getElementById('studentClusteringForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const k = document.getElementById('studentK').value;
            performClustering('students', k);
        });

        // Book Clustering
        document.getElementById('bookClusteringForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const k = document.getElementById('bookK').value;
            performClustering('books', k);
        });

        // Borrowing Pattern Clustering
        document.getElementById('borrowingClusteringForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const k = document.getElementById('borrowingK').value;
            performClustering('borrowing', k);
        });

        function performClustering(type, k) {
            showLoading();
            
            const formData = new FormData();
            formData.append('k', k);
            
            fetch(`/reports/cluster/${type}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    displayResults(type, data.data);
                } else {
                    alert('Clustering failed: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                alert('An error occurred during clustering analysis');
            });
        }

        function displayResults(type, data) {
            const resultsDiv = document.getElementById('clusteringResults');
            const contentDiv = document.getElementById('resultsContent');
            
            let html = '';
            
            if (type === 'students') {
                html = formatStudentResults(data);
            } else if (type === 'books') {
                html = formatBookResults(data);
            } else if (type === 'borrowing') {
                html = formatBorrowingResults(data);
            }
            
            contentDiv.innerHTML = html;
            resultsDiv.style.display = 'block';
            
            // Scroll to results
            resultsDiv.scrollIntoView({ behavior: 'smooth' });
        }

        function formatStudentResults(data) {
            let html = '<div class="results-summary success-summary"><h5>Student Behavior Clusters</h5></div>';
            
            Object.values(data).forEach(cluster => {
                html += `
                    <div class="result-card">
                        <div class="result-card-header">
                            <h6>Cluster ${cluster.cluster_id} - ${cluster.students.length} students</h6>
                        </div>
                        <div class="result-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="result-section-title">Characteristics:</h6>
                                    <ul class="result-list">
                                        <li><strong>Avg Books Borrowed:</strong> ${cluster.characteristics.avg_borrowed}</li>
                                        <li><strong>Avg Overdue Rate:</strong> ${(cluster.characteristics.avg_overdue_rate * 100).toFixed(1)}%</li>
                                        <li><strong>Avg Fines:</strong> ₹${cluster.characteristics.avg_fines}</li>
                                        <li><strong>Avg Compliance:</strong> ${(cluster.characteristics.avg_compliance * 100).toFixed(1)}%</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="result-section-title">Students in this cluster:</h6>
                                    <ul class="result-list">
                                        ${cluster.students.map(s => `<li>${s.student.name}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            return html;
        }

        function formatBookResults(data) {
            let html = '<div class="results-summary success-summary"><h5>Book Usage Clusters</h5></div>';
            
            Object.values(data).forEach(cluster => {
                html += `
                    <div class="result-card">
                        <div class="result-card-header">
                            <h6>Cluster ${cluster.cluster_id} - ${cluster.books.length} books</h6>
                        </div>
                        <div class="result-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="result-section-title">Characteristics:</h6>
                                    <ul class="result-list">
                                        <li><strong>Avg Borrow Count:</strong> ${cluster.characteristics.avg_borrow_count}</li>
                                        <li><strong>Avg Availability:</strong> ${(cluster.characteristics.avg_availability * 100).toFixed(1)}%</li>
                                        <li><strong>Avg Duration:</strong> ${cluster.characteristics.avg_duration} days</li>
                                        <li><strong>Avg Recent Popularity:</strong> ${cluster.characteristics.avg_recent_popularity}</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="result-section-title">Books in this cluster:</h6>
                                    <ul class="result-list">
                                        ${cluster.books.map(b => `<li>${b.book.name}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            return html;
        }

        function formatBorrowingResults(data) {
            let html = '<div class="results-summary success-summary"><h5>Borrowing Pattern Clusters</h5></div>';
            
            Object.values(data).forEach(cluster => {
                html += `
                    <div class="result-card">
                        <div class="result-card-header">
                            <h6>Cluster ${cluster.cluster_id} - ${cluster.patterns.length} patterns</h6>
                        </div>
                        <div class="result-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="result-section-title">Characteristics:</h6>
                                    <ul class="result-list">
                                        <li><strong>Avg Frequency:</strong> ${cluster.characteristics.avg_frequency} per month</li>
                                        <li><strong>Avg Weekday Preference:</strong> ${(cluster.characteristics.avg_weekday_preference * 100).toFixed(1)}%</li>
                                        <li><strong>Avg Return Timing:</strong> ${cluster.characteristics.avg_return_timing} days</li>
                                        <li><strong>Avg Category Preference:</strong> ${(cluster.characteristics.avg_category_preference * 100).toFixed(1)}%</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="result-section-title">Patterns in this cluster:</h6>
                                    <ul class="result-list">
                                        ${cluster.patterns.map(p => `<li>${p.features.student_name}</li>`).join('')}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            return html;
        }

        function showLoading() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('clusteringResults').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }

        function clearResults() {
            document.getElementById('clusteringResults').style.display = 'none';
            document.getElementById('resultsContent').innerHTML = '';
        }

        function exportComprehensiveData() {
            const formats = ['excel', 'csv', 'pdf'];
            const format = prompt('Choose export format (excel, csv, or pdf):', 'excel');
            
            if (format && formats.includes(format.toLowerCase())) {
                window.open(`/reports/clustering/export/comprehensive/${format}`, '_blank');
            }
        }
    </script>

    <style>
        /* Header Styling */
        .clustering-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .clustering-header h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .clustering-header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        /* Info Card Styling */
        .info-card {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }

        .info-icon {
            font-size: 2rem;
            margin-right: 1rem;
            opacity: 0.9;
        }

        .info-content h5 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .info-content p {
            font-size: 1rem;
            margin-bottom: 0;
            opacity: 0.95;
        }

        /* Clustering Cards Styling */
        .clustering-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }

        .clustering-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-header-custom {
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }

        .card-header-custom h5 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .card-description {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        /* Form Styling */
        .clustering-form .form-group {
            margin-bottom: 1.2rem;
        }

        .clustering-form label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .custom-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.6rem;
            font-size: 0.9rem;
            transition: border-color 0.3s ease;
        }

        .custom-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-block {
            width: 100%;
            padding: 0.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 8px;
        }

        /* Quick Actions Styling */
        .quick-actions-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .quick-actions-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1.5rem;
        }

        .quick-actions-buttons .btn {
            margin: 0 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
        }

        /* Results Styling */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        .results-header h4 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .results-summary {
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .success-summary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .success-summary h5 {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
        }

        .result-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .result-card-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .result-card-header h6 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .result-card-body {
            padding: 1.5rem;
        }

        .result-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 1rem;
        }

        .result-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .result-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
            font-size: 0.9rem;
            color: #6c757d;
        }

        .result-list li:last-child {
            border-bottom: none;
        }

        .result-list li strong {
            color: #495057;
        }

        /* Loading Styling */
        .loading-container {
            padding: 3rem;
        }

        .spinner-border {
            width: 4rem;
            height: 4rem;
        }

        .loading-text {
            font-size: 1.1rem;
            color: #6c757d;
            margin-top: 1rem;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .clustering-header h2 {
                font-size: 1.8rem;
            }

            .clustering-header p {
                font-size: 1rem;
            }

            .quick-actions-buttons .btn {
                display: block;
                margin: 0.5rem auto;
                width: 100%;
                max-width: 300px;
            }

            .results-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        @media (max-width: 576px) {
            .clustering-header {
                padding: 1.5rem;
            }

            .info-card {
                flex-direction: column;
                text-align: center;
            }

            .info-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .card-header-custom {
                padding: 1rem;
            }

            .card-body-custom {
                padding: 1rem;
            }
        }
    </style>
@endsection
