<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Category;
use App\Exports\LibraryDataExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\KMeansClusteringService;
use App\Services\DynamicFineCalculator;

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
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "library_comprehensive_report_{$timestamp}";

        switch (strtolower($format)) {
            case 'excel':
                return $this->exportExcel($filename);
            case 'csv':
                return $this->exportCSV($filename);
            case 'pdf':
                return $this->exportPDF($filename);
            default:
                abort(400, 'Invalid export format. Use: excel, csv, or pdf');
        }
    }

    private function exportExcel($filename)
    {
        try {
            return Excel::download(new LibraryDataExport, $filename . '.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportCSV($filename)
    {
        try {
            // Get all data organized by sections
            $allData = $this->getAllLibraryData();
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
                'Cache-Control' => 'no-cache, must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($allData) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for proper UTF-8 encoding in Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                foreach ($allData as $sectionName => $sectionData) {
                    // Add section header
                    fputcsv($file, [strtoupper($sectionName) . ' SECTION']);
                    fputcsv($file, []); // Empty line
                    
                    // Add data
                    foreach ($sectionData as $row) {
                        fputcsv($file, $row);
                    }
                    
                    fputcsv($file, []); // Empty line between sections
                    fputcsv($file, []); // Another empty line
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate CSV file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportPDF($filename)
    {
        try {
            $data = $this->getAllLibraryData();
            
            $pdf = Pdf::loadView('reports.pdf.comprehensive', [
                'data' => $data,
                'timestamp' => now()->format('F j, Y \a\t g:i A'),
                'title' => 'Library Comprehensive Report'
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate PDF file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAllLibraryData()
    {
        $data = [];

        // Books Data
        $books = Book::with(['author', 'category', 'publisher'])
            ->orderBy('name')
            ->get();
            
        $data['BOOKS'] = [
            ['ID', 'Book Name', 'Author', 'Category', 'Publisher', 'ISBN', 'Total Copies', 'Available', 'Status', 'Created Date']
        ];
        
        foreach ($books as $book) {
            $data['BOOKS'][] = [
                $book->id,
                $book->name,
                $book->author->name ?? 'N/A',
                $book->category->name ?? 'N/A',
                $book->publisher->name ?? 'N/A',
                $book->isbn ?? 'N/A',
                $book->total_copies ?? 1,
                $book->available_copies ?? 1,
                $book->status == 'Y' ? 'Active' : 'Inactive',
                $book->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Authors Data
        $authors = Author::withCount('books')->orderBy('name')->get();
        $data['AUTHORS'] = [
            ['ID', 'Author Name', 'Total Books', 'Created Date']
        ];
        
        foreach ($authors as $author) {
            $data['AUTHORS'][] = [
                $author->id,
                $author->name,
                $author->books_count,
                $author->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Publishers Data
        $publishers = Publisher::withCount('books')->orderBy('name')->get();
        $data['PUBLISHERS'] = [
            ['ID', 'Publisher Name', 'Total Books', 'Created Date']
        ];
        
        foreach ($publishers as $publisher) {
            $data['PUBLISHERS'][] = [
                $publisher->id,
                $publisher->name,
                $publisher->books_count,
                $publisher->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Categories Data
        $categories = Category::withCount('books')->orderBy('name')->get();
        $data['CATEGORIES'] = [
            ['ID', 'Category Name', 'Total Books', 'Created Date']
        ];
        
        foreach ($categories as $category) {
            $data['CATEGORIES'][] = [
                $category->id,
                $category->name,
                $category->books_count,
                $category->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Students Data
        $students = Student::withCount('bookIssues')->orderBy('name')->get();
        $data['STUDENTS'] = [
            ['ID', 'Student ID', 'Name', 'Email', 'Phone', 'Address', 'Library Card', 'Total Issues', 'Current Issues', 'Registration Date']
        ];
        
        foreach ($students as $student) {
            $currentIssues = $student->bookIssues()->where('issue_status', 'N')->count();
            $data['STUDENTS'][] = [
                $student->id,
                $student->student_id,
                $student->name,
                $student->email,
                $student->phone,
                $student->address ?? 'N/A',
                $student->library_card_number ?? 'N/A',
                $student->book_issues_count,
                $currentIssues,
                $student->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Book Issues Data
        $bookIssues = BookIssue::with(['book', 'student'])
            ->orderBy('issue_date', 'desc')
            ->get();
            
        $data['BOOK_ISSUES'] = [
            ['Issue ID', 'Book Name', 'Student Name', 'Student ID', 'Issue Date', 'Return Date', 'Status', 'Is Overdue', 'Student Phone', 'Student Email']
        ];
        
        foreach ($bookIssues as $issue) {
            $isOverdue = $issue->issue_status == 'N' && $issue->return_date < now();
            $data['BOOK_ISSUES'][] = [
                $issue->id,
                $issue->book->name ?? 'N/A',
                $issue->student->name ?? 'N/A',
                $issue->student->student_id ?? 'N/A',
                $issue->issue_date->format('Y-m-d'),
                $issue->return_date->format('Y-m-d'),
                $issue->issue_status == 'Y' ? 'Returned' : 'Issued',
                $isOverdue ? 'Yes' : 'No',
                $issue->student->phone ?? 'N/A',
                $issue->student->email ?? 'N/A'
            ];
        }

        return $data;
    }

    /**
     * Export specific data type (books, authors, publishers, categories, students, book_issues)
     */
    public function exportSpecific($type, $format)
    {
        $validTypes = ['books', 'authors', 'publishers', 'categories', 'students', 'book_issues'];
        
        if (!in_array($type, $validTypes)) {
            abort(400, 'Invalid export type. Valid types: ' . implode(', ', $validTypes));
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "library_{$type}_report_{$timestamp}";

        switch (strtolower($format)) {
            case 'excel':
                return $this->exportSpecificExcel($type, $filename);
            case 'csv':
                return $this->exportSpecificCSV($type, $filename);
            case 'pdf':
                return $this->exportSpecificPDF($type, $filename);
            default:
                abort(400, 'Invalid export format. Use: excel, csv, or pdf');
        }
    }

    private function exportSpecificExcel($type, $filename)
    {
        try {
            $data = $this->getSpecificData($type);
            return Excel::download(new class($data) implements FromCollection, WithHeadings, WithStyles {
                private $data;
                
                public function __construct($data) {
                    $this->data = $data;
                }
                
                public function collection() {
                    return collect(array_slice($this->data, 1)); // Skip headers
                }
                
                public function headings(): array {
                    return $this->data[0]; // First row as headers
                }
                
                public function styles(Worksheet $sheet) {
                    return [1 => ['font' => ['bold' => true]]];
                }
            }, $filename . '.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate Excel file: ' . $e->getMessage()], 500);
        }
    }

    private function exportSpecificCSV($type, $filename)
    {
        try {
            $data = $this->getSpecificData($type);
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
                'Cache-Control' => 'no-cache, must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for UTF-8
                
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate CSV file: ' . $e->getMessage()], 500);
        }
    }

    private function exportSpecificPDF($type, $filename)
    {
        try {
            $data = $this->getSpecificData($type);
            
            $pdf = Pdf::loadView('reports.pdf.specific', [
                'data' => $data,
                'type' => $type,
                'timestamp' => now()->format('F j, Y \a\t g:i A'),
                'title' => ucfirst($type) . ' Report'
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate PDF file: ' . $e->getMessage()], 500);
        }
    }

    private function getSpecificData($type)
    {
        switch ($type) {
            case 'books':
                return $this->getBooksData();
            case 'authors':
                return $this->getAuthorsData();
            case 'publishers':
                return $this->getPublishersData();
            case 'categories':
                return $this->getCategoriesData();
            case 'students':
                return $this->getStudentsData();
            case 'book_issues':
                return $this->getBookIssuesData();
            default:
                throw new \Exception('Invalid data type');
        }
    }

    private function getBooksData()
    {
        $books = Book::with(['author', 'category', 'publisher'])
            ->orderBy('name')
            ->get();
            
        $data = [
            ['ID', 'Book Name', 'Author', 'Category', 'Publisher', 'ISBN', 'Total Copies', 'Available Copies', 'Currently Issued', 'Status', 'Created Date']
        ];
        
        foreach ($books as $book) {
            $data[] = [
                $book->id,
                $book->name,
                $book->author->name ?? 'N/A',
                $book->category->name ?? 'N/A',
                $book->publisher->name ?? 'N/A',
                $book->isbn ?? 'N/A',
                $book->total_copies ?? 1,
                $book->available_copies ?? 1,
                $book->bookIssues()->where('issue_status', 'N')->count(),
                $book->status == 'Y' ? 'Active' : 'Inactive',
                $book->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        return $data;
    }

    private function getAuthorsData()
    {
        $authors = Author::withCount('books')->orderBy('name')->get();
        $data = [['ID', 'Author Name', 'Total Books', 'Created Date']];
        
        foreach ($authors as $author) {
            $data[] = [
                $author->id,
                $author->name,
                $author->books_count,
                $author->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        return $data;
    }

    private function getPublishersData()
    {
        $publishers = Publisher::withCount('books')->orderBy('name')->get();
        $data = [['ID', 'Publisher Name', 'Total Books', 'Created Date']];
        
        foreach ($publishers as $publisher) {
            $data[] = [
                $publisher->id,
                $publisher->name,
                $publisher->books_count,
                $publisher->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        return $data;
    }

    private function getCategoriesData()
    {
        $categories = Category::withCount('books')->orderBy('name')->get();
        $data = [['ID', 'Category Name', 'Total Books', 'Created Date']];
        
        foreach ($categories as $category) {
            $data[] = [
                $category->id,
                $category->name,
                $category->books_count,
                $category->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        return $data;
    }

    private function getStudentsData()
    {
        $students = Student::withCount('bookIssues')->orderBy('name')->get();
        $data = [['ID', 'Student ID', 'Name', 'Email', 'Phone', 'Address', 'Library Card', 'Total Issues', 'Current Issues', 'Overdue Issues', 'Registration Date']];
        
        foreach ($students as $student) {
            $currentIssues = $student->bookIssues()->where('issue_status', 'N')->count();
            $overdueIssues = $student->bookIssues()
                ->where('issue_status', 'N')
                ->where('return_date', '<', now())
                ->count();

            $data[] = [
                $student->id,
                $student->student_id,
                $student->name,
                $student->email,
                $student->phone,
                $student->address ?? 'N/A',
                $student->library_card_number ?? 'N/A',
                $student->book_issues_count,
                $currentIssues,
                $overdueIssues,
                $student->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        return $data;
    }

    private function getBookIssuesData()
    {
        $bookIssues = BookIssue::with(['book', 'student'])
            ->orderBy('issue_date', 'desc')
            ->get();
            
        $data = [['Issue ID', 'Book Name', 'Student Name', 'Student ID', 'Issue Date', 'Return Date', 'Actual Return Date', 'Status', 'Is Overdue', 'Overdue Days', 'Student Phone', 'Student Email']];
        
        foreach ($bookIssues as $issue) {
            $isOverdue = $issue->issue_status == 'N' && $issue->return_date < now();
            $overdueDays = $isOverdue ? now()->diffInDays($issue->return_date) : 0;

            $data[] = [
                $issue->id,
                $issue->book->name ?? 'N/A',
                $issue->student->name ?? 'N/A',
                $issue->student->student_id ?? 'N/A',
                $issue->issue_date->format('Y-m-d'),
                $issue->return_date->format('Y-m-d'),
                $issue->actual_return_date ? $issue->actual_return_date->format('Y-m-d') : 'Not Returned',
                $issue->issue_status == 'Y' ? 'Returned' : 'Issued',
                $isOverdue ? 'Yes' : 'No',
                $overdueDays,
                $issue->student->phone ?? 'N/A',
                $issue->student->email ?? 'N/A'
            ];
        }
        
        return $data;
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

    /**
     * Show K-Means clustering analysis
     */
    public function clustering()
    {
        return view('report.clustering');
    }

    /**
     * Perform student behavior clustering
     */
    public function clusterStudents(Request $request)
    {
        $request->validate([
            'k' => 'required|integer|min:2|max:10'
        ]);

        $clusteringService = new KMeansClusteringService();
        $results = $clusteringService->clusterStudentsByBehavior($request->k);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Perform book usage clustering
     */
    public function clusterBooks(Request $request)
    {
        $request->validate([
            'k' => 'required|integer|min:2|max:10'
        ]);

        $clusteringService = new KMeansClusteringService();
        $results = $clusteringService->clusterBooksByUsage($request->k);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Perform borrowing pattern clustering
     */
    public function clusterBorrowingPatterns(Request $request)
    {
        $request->validate([
            'k' => 'required|integer|min:2|max:10'
        ]);

        $clusteringService = new KMeansClusteringService();
        $results = $clusteringService->clusterBorrowingPatterns($request->k);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * Generate comprehensive clustering report
     */
    public function comprehensiveClusteringReport()
    {
        $clusteringService = new KMeansClusteringService();
        
        $studentClusters = $clusteringService->clusterStudentsByBehavior(3);
        $bookClusters = $clusteringService->clusterBooksByUsage(4);
        $borrowingClusters = $clusteringService->clusterBorrowingPatterns(3);

        return view('report.comprehensive-clustering', compact(
            'studentClusters', 'bookClusters', 'borrowingClusters'
        ));
    }

    /**
     * Export clustering results
     */
    public function exportClustering($type, $format)
    {
        $clusteringService = new KMeansClusteringService();
        
        switch ($type) {
            case 'students':
                $data = $clusteringService->clusterStudentsByBehavior(3);
                break;
            case 'books':
                $data = $clusteringService->clusterBooksByUsage(4);
                break;
            case 'borrowing':
                $data = $clusteringService->clusterBorrowingPatterns(3);
                break;
            case 'comprehensive':
                $data = [
                    'students' => $clusteringService->clusterStudentsByBehavior(3),
                    'books' => $clusteringService->clusterBooksByUsage(4),
                    'borrowing' => $clusteringService->clusterBorrowingPatterns(3)
                ];
                break;
            default:
                abort(400, 'Invalid clustering type');
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "clustering_{$type}_{$timestamp}";

        switch (strtolower($format)) {
            case 'excel':
                return $this->exportClusteringExcel($data, $filename);
            case 'csv':
                return $this->exportClusteringCSV($data, $filename);
            case 'pdf':
                return $this->exportClusteringPDF($data, $filename);
            default:
                abort(400, 'Invalid export format');
        }
    }

    private function exportClusteringExcel($data, $filename)
    {
        try {
            // Create a simple export for clustering data
            $exportData = $this->prepareClusteringExportData($data);
            return Excel::download(new LibraryDataExport($exportData), $filename . '.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportClusteringCSV($data, $filename)
    {
        try {
            $exportData = $this->prepareClusteringExportData($data);
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
                'Cache-Control' => 'no-cache, must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($exportData) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                foreach ($exportData as $sectionName => $sectionData) {
                    fputcsv($file, [strtoupper($sectionName) . ' SECTION']);
                    fputcsv($file, []);
                    
                    foreach ($sectionData as $row) {
                        fputcsv($file, $row);
                    }
                    
                    fputcsv($file, []);
                    fputcsv($file, []);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate CSV file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportClusteringPDF($data, $filename)
    {
        try {
            $exportData = $this->prepareClusteringExportData($data);
            
            $pdf = Pdf::loadView('reports.pdf.clustering', [
                'data' => $exportData,
                'timestamp' => now()->format('F j, Y \a\t g:i A'),
                'title' => 'Clustering Analysis Report'
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate PDF file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function prepareClusteringExportData($data)
    {
        $exportData = [];
        
        if (isset($data['students'])) {
            $exportData['Student Clusters'] = $this->formatStudentClustersForExport($data['students']);
        } elseif (isset($data[0]['students'])) {
            $exportData['Student Clusters'] = $this->formatStudentClustersForExport($data);
        }
        
        if (isset($data['books'])) {
            $exportData['Book Clusters'] = $this->formatBookClustersForExport($data['books']);
        } elseif (isset($data[0]['books'])) {
            $exportData['Book Clusters'] = $this->formatBookClustersForExport($data);
        }
        
        if (isset($data['borrowing'])) {
            $exportData['Borrowing Pattern Clusters'] = $this->formatBorrowingClustersForExport($data['borrowing']);
        } elseif (isset($data[0]['patterns'])) {
            $exportData['Borrowing Pattern Clusters'] = $this->formatBorrowingClustersForExport($data);
        }
        
        return $exportData;
    }

    private function formatStudentClustersForExport($clusters)
    {
        $exportData = [['Cluster ID', 'Student Name', 'Total Borrowed', 'Overdue Rate', 'Total Fines', 'Category Diversity', 'Return Compliance']];
        
        foreach ($clusters as $cluster) {
            foreach ($cluster['students'] as $studentData) {
                $exportData[] = [
                    $cluster['cluster_id'],
                    $studentData['student']->name,
                    $studentData['features']['total_borrowed'],
                    round($studentData['features']['overdue_rate'] * 100, 2) . '%',
                    '₹' . $studentData['features']['total_fines'],
                    round($studentData['features']['category_diversity'] * 100, 2) . '%',
                    round($studentData['features']['return_compliance'] * 100, 2) . '%'
                ];
            }
        }
        
        return $exportData;
    }

    private function formatBookClustersForExport($clusters)
    {
        $exportData = [['Cluster ID', 'Book Name', 'Borrow Count', 'Availability Rate', 'Avg Borrow Duration', 'Recent Popularity']];
        
        foreach ($clusters as $cluster) {
            foreach ($cluster['books'] as $bookData) {
                $exportData[] = [
                    $cluster['cluster_id'],
                    $bookData['book']->name,
                    $bookData['features']['borrow_count'],
                    round($bookData['features']['availability_rate'] * 100, 2) . '%',
                    $bookData['features']['avg_borrow_duration'] . ' days',
                    $bookData['features']['recent_popularity']
                ];
            }
        }
        
        return $exportData;
    }

    private function formatBorrowingClustersForExport($clusters)
    {
        $exportData = [['Cluster ID', 'Student Name', 'Monthly Frequency', 'Weekday Preference', 'Return Timing', 'Category Preference']];
        
        foreach ($clusters as $cluster) {
            foreach ($cluster['patterns'] as $patternData) {
                $exportData[] = [
                    $cluster['cluster_id'],
                    $patternData['features']['student_name'],
                    round($patternData['features']['monthly_frequency'], 2) . ' per month',
                    round($patternData['features']['weekday_preference'] * 100, 2) . '%',
                    $patternData['features']['return_timing'] . ' days',
                    round($patternData['features']['book_category_preference'] * 100, 2) . '%'
                ];
            }
        }
        
        return $exportData;
    }
}
