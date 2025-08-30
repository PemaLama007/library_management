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
                        <form action="{{ route('search') }}" method="GET" id="searchForm">
                            <div class="input-group">
                                <input type="text" class="form-control" id="globalSearch" name="q"
                                       placeholder="Search books, students, authors..."
                                       value="{{ request('q') }}" autocomplete="off">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </form>
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
                        <li><a href="{{ route('book_issued') }}">Book Issue</a></li>
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
            
            $('#globalSearch').on('keyup', function() {
                var query = $(this).val();
                
                clearTimeout(searchTimeout);
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(function() {
                        performSearch(query);
                    }, 300);
                } else {
                    $('#searchSuggestions').hide().empty();
                }
            });
            
            function performSearch(query) {
                $.ajax({
                    url: '{{ route('search.ajax') }}',
                    method: 'GET',
                    data: { q: query },
                    success: function(response) {
                        displaySuggestions(response);
                    },
                    error: function() {
                        $('#searchSuggestions').hide();
                    }
                });
            }
            
            function displaySuggestions(data) {
                var suggestions = $('#searchSuggestions');
                suggestions.empty();
                
                if (data.books.length > 0 || data.students.length > 0 || data.authors.length > 0) {
                    var html = '<div class="search-results">';
                    
                    if (data.books.length > 0) {
                        html += '<div class="search-category"><strong>Books</strong></div>';
                        data.books.forEach(function(book) {
                            html += '<div class="search-item" data-type="book" data-id="' + book.id + '">' +
                                   '<i class="fas fa-book"></i> ' + book.name + ' - ' + book.author_name + '</div>';
                        });
                    }
                    
                    if (data.students.length > 0) {
                        html += '<div class="search-category"><strong>Students</strong></div>';
                        data.students.forEach(function(student) {
                            html += '<div class="search-item" data-type="student" data-id="' + student.id + '">' +
                                   '<i class="fas fa-user"></i> ' + student.name + ' (' + student.student_id + ')</div>';
                        });
                    }
                    
                    if (data.authors.length > 0) {
                        html += '<div class="search-category"><strong>Authors</strong></div>';
                        data.authors.forEach(function(author) {
                            html += '<div class="search-item" data-type="author" data-id="' + author.id + '">' +
                                   '<i class="fas fa-user-edit"></i> ' + author.name + '</div>';
                        });
                    }
                    
                    html += '<div class="search-footer">' +
                           '<a href="{{ url("/search") }}?q=' + $('#globalSearch').val() + '" class="btn btn-sm btn-primary">' +
                           'View All Results</a></div>';
                    
                    html += '</div>';
                    suggestions.html(html).show();
                } else {
                    suggestions.hide();
                }
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
    
    <style>
        .search-container {
            position: relative;
        }
        
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .search-category {
            background: #f8f9fa;
            padding: 8px 12px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            color: #1B3F63;
        }
        
        .search-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f5f5f5;
        }
        
        .search-item:hover {
            background: #f8f9fa;
        }
        
        .search-item i {
            margin-right: 8px;
            color: #1B3F63;
        }
        
        .search-footer {
            padding: 10px;
            background: #f8f9fa;
            text-align: center;
            border-top: 1px solid #eee;
        }
    </style>
</body>

</html>
