<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        // Calculate today's issues
        $todayIssues = BookIssue::whereDate('issue_date', Carbon::today())->count();
        
        // Calculate this week's issues
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekIssues = BookIssue::whereBetween('issue_date', [$weekStart, $weekEnd])->count();
        
        // Calculate this month's issues
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthIssues = BookIssue::whereBetween('issue_date', [$monthStart, $monthEnd])->count();
        
        // Calculate overdue books (books not returned after 14 days)
        $overdueDate = Carbon::now()->subDays(14);
        $overdueBooks = BookIssue::where('issue_status', 'N')
            ->where('issue_date', '<', $overdueDate)
            ->count();
        
        // Calculate percentage changes (comparing with previous periods)
        $yesterdayIssues = BookIssue::whereDate('issue_date', Carbon::yesterday())->count();
        $todayChange = $yesterdayIssues > 0 ? round((($todayIssues - $yesterdayIssues) / $yesterdayIssues) * 100, 1) : 0;
        
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $lastWeekIssues = BookIssue::whereBetween('issue_date', [$lastWeekStart, $lastWeekEnd])->count();
        $weekChange = $lastWeekIssues > 0 ? round((($weekIssues - $lastWeekIssues) / $lastWeekIssues) * 100, 1) : 0;
        
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        $lastMonthIssues = BookIssue::whereBetween('issue_date', [$lastMonthStart, $lastMonthEnd])->count();
        $monthChange = $lastMonthIssues > 0 ? round((($monthIssues - $lastMonthIssues) / $lastMonthIssues) * 100, 1) : 0;
        
        // Calculate change in overdue books (compare with last week's overdue count)
        $lastWeekOverdueDate = Carbon::now()->subDays(21); // 21 days ago for comparison
        $lastWeekOverdue = BookIssue::where('issue_status', 'N')
            ->where('issue_date', '<', $lastWeekOverdueDate)
            ->where('issue_date', '>=', Carbon::now()->subDays(28))
            ->count();
        $overdueChange = $overdueBooks - $lastWeekOverdue;
        
        return view('report.index', [
            'todayIssues' => $todayIssues,
            'weekIssues' => $weekIssues,
            'monthIssues' => $monthIssues,
            'overdueBooks' => $overdueBooks,
            'todayChange' => $todayChange,
            'weekChange' => $weekChange,
            'monthChange' => $monthChange,
            'overdueChange' => $overdueChange
        ]);
    }

    public function date_wise()
    {
        return view('report.dateWise', ['books' => '']);
    }

    public function generate_date_wise_report(Request $request)
    {
        $request->validate(['date' => "required|date"]);
        return view('report.dateWise', [
            'books' => BookIssue::where('issue_date', $request->date)->latest()->get()
        ]);
    }

    public function month_wise()
    {
        return view('report.monthWise', ['books' => '']);
    }

    public function generate_month_wise_report(Request $request)
    {
        $request->validate(['month' => "required|date"]);
        return view('report.monthWise', [
            'books' => BookIssue::where('issue_date', 'LIKE', '%' . $request->month . '%')->latest()->get(),
        ]);
    }

    public function not_returned()
    {
        return view('report.notReturned',[
            'books' => BookIssue::where('issue_status', 'N')->latest()->get()
        ]);
    }

    public function export($format)
    {
        // Get all book issues with related data
        $bookIssues = BookIssue::with(['book', 'student'])
            ->orderBy('issue_date', 'desc')
            ->get();
        
        $data = [];
        $data[] = ['Issue ID', 'Book Title', 'Student Name', 'Issue Date', 'Return Date', 'Status'];
        
        foreach ($bookIssues as $issue) {
            $data[] = [
                $issue->id,
                $issue->book ? $issue->book->title : 'N/A',
                $issue->student ? $issue->student->name : 'N/A',
                $issue->issue_date,
                $issue->return_date ?? 'Not Returned',
                $issue->issue_status == 'Y' ? 'Returned' : 'Issued'
            ];
        }
        
        $filename = 'library_report_' . date('Y-m-d_H-i-s');
        
        switch (strtolower($format)) {
            case 'csv':
                return $this->exportCSV($data, $filename);
            case 'excel':
                return $this->exportExcel($data, $filename);
            case 'pdf':
                return $this->exportPDF($data, $filename);
            default:
                abort(400, 'Invalid export format');
        }
    }
    
    private function exportCSV($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportExcel($data, $filename)
    {
        // For simplicity, we'll export as CSV with .xlsx extension
        // In a real application, you'd use a library like PhpSpreadsheet
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xlsx"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    private function exportPDF($data, $filename)
    {
        // Simple HTML to PDF conversion using DomPDF would require additional setup
        // For now, we'll return a simple HTML response that can be printed to PDF
        $html = '<html><head><title>Library Report</title>';
        $html .= '<style>table { border-collapse: collapse; width: 100%; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        $html .= 'th { background-color: #f2f2f2; }</style></head><body>';
        $html .= '<h1>Library Report - ' . date('Y-m-d H:i:s') . '</h1>';
        $html .= '<table>';
        
        $isHeader = true;
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $tag = $isHeader ? 'th' : 'td';
                $html .= '<' . $tag . '>' . htmlspecialchars($cell) . '</' . $tag . '>';
            }
            $html .= '</tr>';
            $isHeader = false;
        }
        
        $html .= '</table></body></html>';
        
        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.html"',
        ]);
    }

    /**
     * Show inventory report
     */
    public function inventory()
    {
        $books = Book::with(['author', 'category', 'publisher'])
            ->selectRaw('books.*,
                COALESCE(books.total_copies, 1) as total_copies,
                COALESCE(books.available_copies, 1) as available_copies,
                (SELECT COUNT(*) FROM book_issues WHERE book_issues.book_id = books.id AND book_issues.issue_status = "N") as currently_issued')
            ->orderBy('name')
            ->get();

        // Calculate summary statistics
        $totalBooks = $books->count();
        $totalCopies = $books->sum('total_copies');
        $totalAvailable = $books->sum('available_copies');
        $totalIssued = $books->sum('currently_issued');
        $outOfStock = $books->where('available_copies', 0)->count();
        $lowStock = $books->where('available_copies', '>', 0)->where('available_copies', '<=', 2)->count();

        return view('report.inventory', compact(
            'books', 'totalBooks', 'totalCopies', 'totalAvailable',
            'totalIssued', 'outOfStock', 'lowStock'
        ));
    }
}
