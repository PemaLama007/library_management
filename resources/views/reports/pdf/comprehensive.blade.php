<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007bff;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            color: #495057;
        }
        
        td {
            border: 1px solid #dee2e6;
            padding: 6px;
            vertical-align: top;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        @media print {
            body { margin: 0; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on {{ $timestamp }}</p>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-item">
            <span class="stat-number">{{ count($data['BOOKS']) - 1 }}</span>
            <div class="stat-label">Total Books</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ count($data['AUTHORS']) - 1 }}</span>
            <div class="stat-label">Authors</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ count($data['STUDENTS']) - 1 }}</span>
            <div class="stat-label">Students</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ count($data['BOOK_ISSUES']) - 1 }}</span>
            <div class="stat-label">Book Issues</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ count($data['CATEGORIES']) - 1 }}</span>
            <div class="stat-label">Categories</div>
        </div>
        <div class="stat-item">
            <span class="stat-number">{{ count($data['PUBLISHERS']) - 1 }}</span>
            <div class="stat-label">Publishers</div>
        </div>
    </div>

    @foreach($data as $sectionName => $sectionData)
        <div class="section">
            <div class="section-title">
                {{ str_replace('_', ' ', strtoupper($sectionName)) }}
            </div>
            
            <table>
                @foreach($sectionData as $index => $row)
                    @if($index === 0)
                        <thead>
                            <tr>
                                @foreach($row as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                    @else
                        <tr>
                            @foreach($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endif
                    
                    @if($index === count($sectionData) - 1)
                        </tbody>
                    @endif
                @endforeach
            </table>
        </div>
    @endforeach

    <div class="footer">
        <p>Library Management System - Comprehensive Report</p>
        <p>This report contains {{ count($data) }} sections with complete library data as of {{ $timestamp }}</p>
    </div>
</body>
</html>
