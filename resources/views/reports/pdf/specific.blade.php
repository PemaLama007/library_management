<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }} - Library Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .report-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .data-table th {
            background-color: #007bff;
            color: white;
            padding: 12px 8px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 12px;
        }
        .data-table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .data-table tbody tr:hover {
            background-color: #e8f4fd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .total-count {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Library Management System</p>
        <p>Generated on: {{ $timestamp }}</p>
    </div>

    <div class="report-info">
        <strong>Report Type:</strong> {{ ucfirst($type) }} Export<br>
        <strong>Total Records:</strong> {{ count($data) - 1 }} records
    </div>

    <table class="data-table">
        <thead>
            <tr>
                @foreach($data[0] as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i < count($data); $i++)
                <tr>
                    @foreach($data[$i] as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="total-count">
        Total {{ ucfirst($type) }}: {{ count($data) - 1 }}
    </div>

    <div class="footer">
        <p>This report was generated automatically by the Library Management System.</p>
        <p>© {{ date('Y') }} Library Management System. All rights reserved.</p>
    </div>
</body>
</html>
