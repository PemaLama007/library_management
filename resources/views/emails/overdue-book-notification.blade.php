<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overdue Book Notice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .book-details {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
            border-radius: 3px;
        }
        .fine-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            margin: 15px 0;
            border-radius: 3px;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
        }
        .urgent {
            color: #dc3545;
            font-weight: bold;
        }
        .info {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 Library Management System</h1>
        <h2>Overdue Book Notice</h2>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $student->name }}</strong>,</p>
        
        <p>This is an important notice regarding your borrowed book from the library.</p>
        
        <div class="book-details">
            <h3>Book Details:</h3>
            <p><strong>Book Title:</strong> {{ $book->name }}</p>
            <p><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($issueDate)->format('M d, Y') }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($returnDate)->format('M d, Y') }}</p>
            <p><strong>Overdue Days:</strong> <span class="urgent">{{ $overdueDays }} day(s)</span></p>
        </div>
        
        <div class="fine-notice">
            <h3>⚠️ Fine Information:</h3>
            <p>Your book is currently overdue, and fines have been accumulating.</p>
            <p><strong>Current Fine Amount:</strong> <span class="urgent">${{ number_format($fineAmount, 2) }}</span></p>
            <p><em>Fines continue to accumulate daily until the book is returned.</em></p>
        </div>
        
        <h3>Required Action:</h3>
        <p>Please return the book to the library <strong>immediately</strong> to:</p>
        <ul>
            <li>Stop further fine accumulation</li>
            <li>Maintain your good standing with the library</li>
            <li>Allow other students to access this resource</li>
        </ul>
        
        <p>If you have any questions or need to discuss your situation, please contact the library staff.</p>
        
        <p>Thank you for your prompt attention to this matter.</p>
        
        <p>Best regards,<br>
        <strong>Library Management Team</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification. Please do not reply to this email.</p>
        <p>Library Management System | Student ID: {{ $student->student_id ?? 'N/A' }}</p>
    </div>
</body>
</html>
