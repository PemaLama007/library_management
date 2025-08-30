@extends("layouts.app")
@section('content')
    <div id="admin-content">
        <div class="container">
            <div class="row">
                <div class="offset-md-3 col-md-6">
                    <h2 class="admin-heading text-center">Not Returned Books</h2>
                </div>
            </div>
            @if ($books)
                <div class="row">
                    <div class="col-md-12">
                        <table class="content-table">
                            <thead>
                                <th>S.No</th>
                                <th>Student Name</th>
                                <th>Book Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Issue Date</th>
                                <th>Return Date</th>
                                <th>Over Days</th>
                                <th>Fine</th>
                            </thead>
                            <tbody>
                                @forelse ($books as $book)
                                    <tr>
                                        <td>{{ $book->id }}</td>
                                        <td>{{ $book->student->name }}</td>
                                        <td>{{ $book->book->name }}</td>
                                        <td>{{ $book->student->phone }}</td>
                                        <td>{{ $book->student->email }}</td>
                                        <td>{{ $book->issue_date->format('d M, Y') }}</td>
                                        <td>{{ $book->return_date->format('d M, Y') }}</td>
                                        <td>@php $date1 = date_create(date('Y-m-d'));
                                            $date2 = date_create($book->return_date->format('Y-m-d'));
                                            if($date1 > $date2){
                                              $diff = date_diff($date2, $date1);
                                              echo $days = $diff->format('%a days');
                                            }else{
                                              echo '0 days';
                                            } @endphp</td>
                                        <td>@php 
                                            $currentDate = date_create(date('Y-m-d'));
                                            $returnDate = date_create($book->return_date->format('Y-m-d'));
                                            if($currentDate > $returnDate){
                                              $diff = date_diff($returnDate, $currentDate);
                                              $overdueDays = $diff->format('%a');
                                              $finePerDay = \App\Models\settings::latest()->first()->fine ?? 0;
                                              $totalFine = $overdueDays * $finePerDay;
                                              echo '$' . $totalFine;
                                            }else{
                                              echo '$0';
                                            } @endphp</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">No Record Found!</td>
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
