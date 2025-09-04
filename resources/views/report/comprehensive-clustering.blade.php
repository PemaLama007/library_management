@extends('layouts.app')

@section('title', 'Comprehensive Clustering Report')

@section('content')
<div class="container">
    <h1 class="mb-4">Comprehensive Clustering Report</h1>
    <p>This report provides a detailed clustering analysis of students, books, and borrowing patterns.</p>

    <h2>Student Clusters</h2>
    @if(isset($studentClusters) && count($studentClusters))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cluster ID</th>
                    <th>Student Count</th>
                    <th>Students</th>
                    <th>Characteristics</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentClusters as $clusterId => $clusterData)
                    <tr>
                        <td>{{ $clusterId }}</td>
                        <td>{{ isset($clusterData['students']) ? count($clusterData['students']) : 0 }}</td>
                        <td>
                            @if(isset($clusterData['students']))
                                @foreach($clusterData['students'] as $studentData)
                                    <span class="badge badge-primary">{{ $studentData['student']->name ?? 'Unknown Student' }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(isset($clusterData['characteristics']))
                                <small>{{ is_array($clusterData['characteristics']) ? json_encode($clusterData['characteristics']) : $clusterData['characteristics'] }}</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No student clustering data available.</p>
    @endif

    <h2>Book Clusters</h2>
    @if(isset($bookClusters) && count($bookClusters))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cluster ID</th>
                    <th>Book Count</th>
                    <th>Books</th>
                    <th>Characteristics</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookClusters as $clusterId => $clusterData)
                    <tr>
                        <td>{{ $clusterId }}</td>
                        <td>{{ isset($clusterData['books']) ? count($clusterData['books']) : 0 }}</td>
                        <td>
                            @if(isset($clusterData['books']))
                                @foreach($clusterData['books'] as $bookData)
                                    <span class="badge badge-success">{{ $bookData['book']->name ?? 'Unknown Book' }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(isset($clusterData['characteristics']))
                                <small>{{ is_array($clusterData['characteristics']) ? json_encode($clusterData['characteristics']) : $clusterData['characteristics'] }}</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No book clustering data available.</p>
    @endif

    <h2>Borrowing Pattern Clusters</h2>
    @if(isset($borrowingClusters) && count($borrowingClusters))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cluster ID</th>
                    <th>Pattern Count</th>
                    <th>Students in Cluster</th>
                    <th>Characteristics</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrowingClusters as $clusterId => $clusterData)
                    <tr>
                        <td>{{ $clusterId }}</td>
                        <td>{{ isset($clusterData['patterns']) ? count($clusterData['patterns']) : 0 }}</td>
                        <td>
                            @if(isset($clusterData['patterns']))
                                @foreach($clusterData['patterns'] as $patternData)
                                    <span class="badge badge-info">{{ $patternData['features']['student_name'] ?? 'Student ID: ' . ($patternData['features']['student_id'] ?? 'Unknown') }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(isset($clusterData['characteristics']))
                                <small>{{ is_array($clusterData['characteristics']) ? json_encode($clusterData['characteristics']) : $clusterData['characteristics'] }}</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No borrowing pattern clustering data available.</p>
    @endif
</div>
@endsection
