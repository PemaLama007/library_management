<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Library Management System') }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}"> <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }} "> <!-- Custom stlylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Font Awesome -->
</head>

<body>
    <div id="header">
        <!-- HEADER -->
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <div class="logo">
                        <a href="{{ route('dashboard') }}">
                            <h1 class="text-uppercase mb-0" style="font-weight: 900; font-size: 2.5rem; letter-spacing: 2px; color: #1B3F63;">E LIBRARY</h1>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <!-- Global Search Bar -->
                    <div class="search-container">
                        <div class="input-group">
                            <input type="text" class="form-control" id="globalSearch" name="q"
                                   placeholder="Search books, students, authors..."
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                        <div id="searchSuggestions" class="search-suggestions" style="display: none;"></div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Hi {{ auth()->user()->name }}
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('change_password') }}">Change Password</a>
                            <a class="dropdown-item" href="#" onclick="document.getElementById('logoutForm').submit()">Log Out</a>
                        </div>
                        <form method="post" id="logoutForm" action="{{ route('logout') }}">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /HEADER -->
    <div id="menubar">
        <!-- Menu Bar -->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="menu">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('authors') }}">Authors</a></li>
                        <li><a href="{{ route('publishers') }}">Publishers</a></li>
                        <li><a href="{{ route('categories') }}">Categories</a></li>
                        <li><a href="{{ route('books') }}">Books</a></li>
                        <li><a href="{{ route('students') }}">Reg Students</a></li>
                        <li><a href="{{ route('book_issue') }}">Book Issue</a></li>
                        <li><a href="{{ route('reports') }}">Reports</a></li>
                        <li><a href="{{ route('settings') }}">Settings</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div> <!-- /Menu Bar -->

    @yield('content')
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    <!-- Global Search JavaScript -->
    <script>
        $(document).ready(function() {
            var searchTimeout;
            
            // Handle search button click and Enter key press
            function triggerSearch() {
                var query = $('#globalSearch').val().trim();
                
                if (query.length >= 1) {
                    performSearch(query, true); // true indicates this is from manual search
                } else {
                    $('#searchSuggestions').hide().empty();
                }
            }
            
            // Search button click
            $('#searchBtn').on('click', function(e) {
                e.preventDefault();
                triggerSearch();
            });
            
            // Enter key press on search input
            $('#globalSearch').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    triggerSearch();
                }
            });
            
            // Typing in search input (for suggestions)
            $('#globalSearch').on('keyup', function(e) {
                if (e.which === 13) return; // Ignore Enter key for keyup
                
                var query = $(this).val();
                
                clearTimeout(searchTimeout);
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        performSearch(query, false); // false indicates this is from typing
                    }, 300);
                } else {
                    $('#searchSuggestions').hide().empty();
                }
            });
            
            function performSearch(query, isFormSubmission) {
                $.ajax({
                    url: '{{ route('search.ajax') }}',
                    method: 'GET',
                    data: { q: query },
                    success: function(response) {
                        displaySuggestions(response, isFormSubmission);
                    },
                    error: function(xhr, status, error) {
                        $('#searchSuggestions').hide();
                        if (isFormSubmission) {
                            showNotFoundDialog(query);
                        }
                    }
                });
            }
            
            function displaySuggestions(data, isFormSubmission) {
                var suggestions = $('#searchSuggestions');
                suggestions.empty();
                
                var hasResults = data.books.length > 0 || data.students.length > 0 || data.authors.length > 0 || 
                                data.categories.length > 0 || data.publishers.length > 0 || data.book_issues.length > 0;
                
                if (hasResults) {
                    var html = '<div class="search-results">';
                    
                    if (data.books && data.books.length > 0) {
                        html += '<div class="search-category"><strong>Books</strong></div>';
                        data.books.forEach(function(book) {
                            html += '<div class="search-item" data-type="book" data-id="' + book.id + '">' +
                                   '<i class="fas fa-book"></i> ' + book.name + ' - ' + book.author_name + '</div>';
                        });
                    }
                    
                    if (data.students && data.students.length > 0) {
                        html += '<div class="search-category"><strong>Students</strong></div>';
                        data.students.forEach(function(student) {
                            html += '<div class="search-item" data-type="student" data-id="' + student.id + '">' +
                                   '<i class="fas fa-user"></i> ' + student.name + ' (' + student.student_id + ')</div>';
                        });
                    }
                    
                    if (data.authors && data.authors.length > 0) {
                        html += '<div class="search-category"><strong>Authors</strong></div>';
                        data.authors.forEach(function(author) {
                            html += '<div class="search-item" data-type="author" data-id="' + author.id + '">' +
                                   '<i class="fas fa-user-edit"></i> ' + author.name + '</div>';
                        });
                    }
                    
                    if (data.categories && data.categories.length > 0) {
                        html += '<div class="search-category"><strong>Categories</strong></div>';
                        data.categories.forEach(function(category) {
                            html += '<div class="search-item" data-type="category" data-id="' + category.id + '">' +
                                   '<i class="fas fa-tags"></i> ' + category.name + '</div>';
                        });
                    }
                    
                    if (data.publishers && data.publishers.length > 0) {
                        html += '<div class="search-category"><strong>Publishers</strong></div>';
                        data.publishers.forEach(function(publisher) {
                            html += '<div class="search-item" data-type="publisher" data-id="' + publisher.id + '">' +
                                   '<i class="fas fa-building"></i> ' + publisher.name + '</div>';
                        });
                    }
                    
                    if (data.book_issues && data.book_issues.length > 0) {
                        html += '<div class="search-category"><strong>Book Issues</strong></div>';
                        data.book_issues.forEach(function(issue) {
                            html += '<div class="search-item" data-type="book_issue" data-id="' + issue.id + '">' +
                                   '<i class="fas fa-exchange-alt"></i> ' + issue.book_name + ' - ' + issue.student_name + '</div>';
                        });
                    }
                    
                    if (!isFormSubmission) {
                        // Removed the "View All Results" footer as requested
                    }
                    
                    html += '</div>';
                    suggestions.html(html).show();
                } else {
                    suggestions.hide();
                    if (isFormSubmission) {
                        showNotFoundDialog($('#globalSearch').val());
                    }
                }
            }
            
            function showNotFoundDialog(query) {
                // Create a Bootstrap modal for better UX
                var modalHtml = `
                    <div class="modal fade" id="noResultsModal" tabindex="-1" role="dialog" aria-labelledby="noResultsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-warning text-white">
                                    <h5 class="modal-title" id="noResultsModalLabel">
                                        <i class="fas fa-search-minus me-2"></i>No Results Found
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-search" style="font-size: 3rem; color: #ffc107;"></i>
                                    </div>
                                    <h6 class="mb-3">No results found for "<strong>${query}</strong>"</h6>
                                    <p class="text-muted">Try the following suggestions:</p>
                                    <ul class="list-unstyled text-left">
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Check your spelling</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Use different keywords</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Search with fewer words</li>
                                        <li><i class="fas fa-check-circle text-success me-2"></i>Try searching for partial names</li>
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                                        <i class="fas fa-times me-1"></i>Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                $('#noResultsModal').remove();
                
                // Add modal to body
                $('body').append(modalHtml);
                
                // Show modal
                $('#noResultsModal').modal('show');
                
                // Remove modal from DOM after it's hidden
                $('#noResultsModal').on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }
            
            // Handle click on search suggestions
            $(document).on('click', '.search-item', function() {
                var type = $(this).data('type');
                var id = $(this).data('id');
                
                switch(type) {
                    case 'book':
                        window.location.href = '{{ url("/books") }}/' + id;
                        break;
                    case 'student':
                        window.location.href = '{{ url("/students") }}/' + id;
                        break;
                    case 'author':
                        window.location.href = '{{ url("/authors") }}/' + id;
                        break;
                    case 'category':
                        window.location.href = '{{ url("/categories") }}/' + id;
                        break;
                    case 'publisher':
                        window.location.href = '{{ url("/publishers") }}/' + id;
                        break;
                    case 'book_issue':
                        window.location.href = '{{ url("/book-issue") }}/' + id;
                        break;
                }
            });
            
            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-container').length) {
                    $('#searchSuggestions').hide();
                }
            });
        });
    </script>
</body>

</html>
