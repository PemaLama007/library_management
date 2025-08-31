@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-boxes"></i>
                        Book Inventory Report
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-lg-2 col-md-4 col-sm-6 align-items-center">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $totalBooks }}</h3>
                                    <p>Total Books</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $totalCopies }}</h3>
                                    <p>Total Copies</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-copy"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $totalAvailable }}</h3>
                                    <p>Available</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $totalIssued }}</h3>
                                    <p>Currently Issued</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-hand-holding"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $outOfStock }}</h3>
                                    <p>Out of Stock</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="small-box bg-secondary">
                                <div class="inner">
                                    <h3>{{ $lowStock }}</h3>
                                    <p>Low Stock (≤2)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="inventoryTable">
                            <thead>
                                <tr>
                                    <th>Book ID</th>
                                    <th>Book Name</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Publisher</th>
                                    <th>ISBN</th>
                                    <th>Total Copies</th>
                                    <th>Available</th>
                                    <th>Issued</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $book)
                                <tr>
                                    <td>{{ $book->id }}</td>
                                    <td>{{ $book->name }}</td>
                                    <td>{{ $book->author->name ?? 'N/A' }}</td>
                                    <td>{{ $book->category->name ?? 'N/A' }}</td>
                                    <td>{{ $book->publisher->name ?? 'N/A' }}</td>
                                    <td>{{ $book->isbn ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $book->total_copies }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $book->available_copies > 0 ? 'success' : 'danger' }}">
                                            {{ $book->available_copies }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $book->currently_issued }}</span>
                                    </td>
                                    <td>
                                        @if($book->available_copies == 0)
                                            <span class="badge badge-danger">Out of Stock</span>
                                        @elseif($book->available_copies <= 2)
                                            <span class="badge badge-warning">Low Stock</span>
                                        @else
                                            <span class="badge badge-success">In Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No books found in inventory</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    .card-tools {
        display: none !important;
    }
    
    .small-box {
        break-inside: avoid;
    }
    
    .table {
        break-inside: avoid;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#inventoryTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']],
        columnDefs: [
            {
                targets: [6, 7, 8, 9],
                orderable: true,
                searchable: false
            }
        ],
        language: {
            search: "Search books:",
            lengthMenu: "Show _MENU_ books per page",
            info: "Showing _START_ to _END_ of _TOTAL_ books",
            infoEmpty: "No books available",
            infoFiltered: "(filtered from _MAX_ total books)"
        }
    });
});
</script>
@endsection
