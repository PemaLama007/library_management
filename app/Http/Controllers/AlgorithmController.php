<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DynamicFineCalculator;
use App\Services\LibraryFraudDetection;
use App\Services\SimpleBookRecommendation;
use App\Models\Student;
use App\Models\BookIssue;

class AlgorithmController extends Controller
{
    /**
     * Display algorithm dashboard
     */
    public function index()
    {
        $fraudDetection = new LibraryFraudDetection();
        $recommendationEngine = new SimpleBookRecommendation();
        
        // Get high-risk students
        $highRiskStudents = $fraudDetection->bulkFraudDetection(20);
        
        // Get trending books
        $trendingBooks = $recommendationEngine->getTrendingBooks(5);
        
        // Get some statistics
        $totalOverdueBooks = BookIssue::where('issue_status', 'N')
            ->where('return_date', '<', now())
            ->count();
            
        return view('algorithms.dashboard', [
            'highRiskStudents' => $highRiskStudents,
            'trendingBooks' => $trendingBooks,
            'totalOverdueBooks' => $totalOverdueBooks
        ]);
    }
    
    /**
     * Show detailed fine analysis for a student
     */
    public function showFineAnalysis(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        $fineCalculator = new DynamicFineCalculator();
        
        // Get all overdue books for this student
        $overdueBooks = $student->bookIssues()
            ->where('issue_status', 'N')
            ->where('return_date', '<', now())
            ->with(['book'])
            ->get();
            
        $fineAnalysis = [];
        $totalFine = 0;
        
        foreach ($overdueBooks as $bookIssue) {
            $fineData = $fineCalculator->calculateProgressiveFine(
                $bookIssue->issue_date,
                $bookIssue->return_date
            );
            
            $fineAnalysis[] = [
                'book_issue' => $bookIssue,
                'fine_data' => $fineData
            ];
            
            $totalFine += $fineData['fine_amount'];
        }
        
        // Calculate potential remission
        $remissionData = $fineCalculator->calculateFineRemission($studentId, $totalFine);
        
        return view('algorithms.fine-analysis', [
            'student' => $student,
            'fineAnalysis' => $fineAnalysis,
            'totalFine' => $totalFine,
            'remissionData' => $remissionData
        ]);
    }
    
    /**
     * Show fraud analysis for a student
     */
    public function showFraudAnalysis($studentId)
    {
        $student = Student::findOrFail($studentId);
        $fraudDetection = new LibraryFraudDetection();
        
        $fraudAnalysis = $fraudDetection->detectStudentAnomalies($studentId);
        
        return view('algorithms.fraud-analysis', [
            'student' => $student,
            'fraudAnalysis' => $fraudAnalysis
        ]);
    }
    
    /**
     * Show book recommendations for a student
     */
    public function showRecommendations($studentId)
    {
        $student = Student::findOrFail($studentId);
        $recommendationEngine = new SimpleBookRecommendation();
        
        $recommendations = $recommendationEngine->getRecommendationsForStudent($studentId, 10);
        $trendingBooks = $recommendationEngine->getTrendingBooks(5);
        $newArrivals = $recommendationEngine->getNewArrivals(5);
        
        return view('algorithms.recommendations', [
            'student' => $student,
            'recommendations' => $recommendations,
            'trendingBooks' => $trendingBooks,
            'newArrivals' => $newArrivals
        ]);
    }
}
