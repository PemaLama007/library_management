@extends('layouts.app')
@section('content')
    <div id="admin-content">
        <div class="container">
            <!-- Reports Header -->
            <div class="row mb-4">
                <div class="col-12 ">
                    <div class="reports-header">
                        <h2 class="admin-heading text-white"><i class="fas fa-chart-bar"></i> Reports & Analytics</h2>
                        <p class="text-white">Generate comprehensive reports to analyze your library's performance</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Summary -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="report-stat-card">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <h5>Today's Issues</h5>
                            <h3>{{ $todayIssues }}</h3>
                            @if ($todayChange > 0)
                                <span class="text-success"><i class="fas fa-arrow-up"></i> +{{ $todayChange }}%</span>
                            @elseif($todayChange < 0)
                                <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ $todayChange }}%</span>
                            @else
                                <span class="text-muted"><i class="fas fa-minus"></i> No change</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="report-stat-card">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div class="stat-content">
                            <h5>This Week</h5>
                            <h3>{{ $weekIssues }}</h3>
                            @if ($weekChange > 0)
                                <span class="text-success"><i class="fas fa-arrow-up"></i> +{{ $weekChange }}%</span>
                            @elseif($weekChange < 0)
                                <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ $weekChange }}%</span>
                            @else
                                <span class="text-muted"><i class="fas fa-minus"></i> No change</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="report-stat-card">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h5>Overdue Books</h5>
                            <h3>{{ $overdueBooks }}</h3>
                            @if ($overdueChange > 0)
                                <span class="text-danger"><i class="fas fa-arrow-up"></i> +{{ $overdueChange }}</span>
                            @elseif($overdueChange < 0)
                                <span class="text-success"><i class="fas fa-arrow-down"></i> {{ $overdueChange }}</span>
                            @else
                                <span class="text-muted"><i class="fas fa-minus"></i> No change</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="report-stat-card">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h5>This Month</h5>
                            <h3>{{ $monthIssues }}</h3>
                            @if ($monthChange > 0)
                                <span class="text-success"><i class="fas fa-arrow-up"></i> +{{ $monthChange }}%</span>
                            @elseif($monthChange < 0)
                                <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ $monthChange }}%</span>
                            @else
                                <span class="text-muted"><i class="fas fa-minus"></i> No change</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Types -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4><i class="fas fa-chart-line"></i> Available Reports</h4>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card report-card">
                        <div class="card-body text-center">
                            <div class="report-icon bg-primary text-white mb-3">
                                <i class="fas fa-boxes fa-2x"></i>
                            </div>
                            <h5 class="card-title">Book Inventory Report</h5>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Report
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card report-card">
                        <div class="card-body text-center">
                            <div class="report-icon bg-info text-white mb-3">
                                <i class="fas fa-chart-pie fa-2x"></i>
                            </div>
                            <h5 class="card-title">K-Means Clustering Analysis</h5>
                            <a href="{{ route('reports.clustering') }}" class="btn btn-info">
                                <i class="fas fa-brain"></i> Analyze Data
                            </a>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card report-card">
                        <div class="card-body text-center">
                            <div class="report-icon bg-success text-white mb-3">
                                <i class="fas fa-chart-bar fa-2x"></i>
                            </div>
                            <h5 class="card-title">Issue History Report</h5>
                            <button class="btn btn-success" disabled>
                                <i class="fas fa-clock"></i> Coming Soon
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card report-card">
                        <div class="card-body text-center">
                            <div class="report-icon bg-warning text-white mb-3">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <h5 class="card-title">Overdue Books Report</h5>
                            <button class="btn btn-warning" disabled>
                                <i class="fas fa-clock"></i> Coming Soon
                            </button>
                        </div>
                    </div>
                </div> --}}
            </div>

            <!-- Quick Export Options -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="export-section">
                        <h4><i class="fas fa-download"></i> Quick Export Options</h4>
                        <div class="export-buttons">
                            <button class="btn btn-outline-danger" onclick="exportData('pdf')">
                                <i class="fas fa-file-pdf"></i> Export as PDF
                            </button>
                            <button class="btn btn-outline-success" onclick="exportData('excel')">
                                <i class="fas fa-file-excel"></i> Export as Excel
                            </button>
                            <button class="btn btn-outline-info" onclick="exportData('csv')">
                                <i class="fas fa-file-csv"></i> Export as CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function exportData(format) {
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';
            button.disabled = true;

            // Create download URL and trigger download
            const downloadUrl = `/reports/export/${format}`;

            // Use fetch to handle the download properly
            fetch(downloadUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Export failed');
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    
                    // Set filename based on format
                    const timestamp = new Date().toISOString().split('T')[0];
                    const extension = format === 'excel' ? 'xlsx' : format;
                    link.download = `library_comprehensive_report_${timestamp}.${extension}`;
                    
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Clean up
                    window.URL.revokeObjectURL(url);
                    
                    // Show success message
                    alert(`${format.toUpperCase()} file has been downloaded successfully!`);
                })
                .catch(error => {
                    console.error('Export error:', error);
                    alert(`Failed to export ${format.toUpperCase()} file. Please try again.`);
                })
                .finally(() => {
                    // Reset button state
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, 1000);
                });
        }
    </script>

    <style>
        .report-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .report-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .report-stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            height: 100%;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
        }

        .stat-content h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: bold;
        }

        .stat-content h5 {
            margin: 0 0 5px 0;
            color: #666;
            font-size: 0.9rem;
        }

        .reports-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        .export-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .export-buttons {
            margin-top: 15px;
        }

        .export-buttons .btn {
            margin: 0 5px;
        }
    </style>
@endsection
