<?php

namespace App\Services;

use App\Models\BookIssue;
use App\Models\Student;
use Carbon\Carbon;

/**
 * Basic Fraud Detection for Library System
 * Detects suspicious patterns without complex user roles
 */
class LibraryFraudDetection
{
    /**
     * Detect suspicious student behavior patterns
     */
    public function detectStudentAnomalies($studentId)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return ['risk_level' => 'unknown', 'score' => 0, 'flags' => []];
        }

        $riskScore = 0;
        $flags = [];

        // Pattern 1: Rapid consecutive book issues (5+ books in 7 days)
        $recentIssues = $student->bookIssues()
            ->where('issue_date', '>=', now()->subDays(7))
            ->count();

        if ($recentIssues > 5) {
            $riskScore += 30;
            $flags[] = "Rapid issuing: {$recentIssues} books in 7 days";
        }

        // Pattern 2: High overdue rate (>60% of books overdue)
        $overdueRate = $this->calculateOverdueRate($studentId);
        if ($overdueRate > 0.6) {
            $riskScore += 40;
            $flags[] = "High overdue rate: " . round($overdueRate * 100, 1) . "%";
        }

        // Pattern 3: Never returned books (books issued >30 days ago, still not returned)
        $neverReturned = $student->bookIssues()
            ->where('issue_status', 'N')
            ->where('issue_date', '<', now()->subDays(30))
            ->count();

        if ($neverReturned > 2) {
            $riskScore += 50;
            $flags[] = "Books not returned for 30+ days: {$neverReturned}";
        }

        // Pattern 4: Duplicate contact information (potential multiple accounts)
        $duplicatesByEmail = Student::where('email', $student->email)
            ->where('id', '!=', $student->id)
            ->count();

        $duplicatesByPhone = Student::where('phone', $student->phone)
            ->where('id', '!=', $student->id)
            ->count();

        if ($duplicatesByEmail > 0 || $duplicatesByPhone > 0) {
            $riskScore += 35;
            $flags[] = "Potential duplicate accounts detected";
        }

        // Pattern 5: Unusual fine accumulation
        $totalFines = $this->calculateTotalOutstandingFines($studentId);
        if ($totalFines > 500) { // Threshold: ₹500
            $riskScore += 25;
            $flags[] = "High outstanding fines: ₹{$totalFines}";
        }

        return [
            'risk_level' => $this->getRiskLevel($riskScore),
            'score' => $riskScore,
            'flags' => $flags,
            'student_id' => $studentId,
            'checked_at' => now()->toDateTimeString()
        ];
    }

    /**
     * Calculate overdue rate for a student
     */
    private function calculateOverdueRate($studentId)
    {
        $allIssues = BookIssue::where('student_id', $studentId)->count();
        
        if ($allIssues === 0) {
            return 0;
        }

        $overdueIssues = BookIssue::where('student_id', $studentId)
            ->where('issue_status', 'N')
            ->where('return_date', '<', now())
            ->count();

        return $overdueIssues / $allIssues;
    }

    /**
     * Calculate total outstanding fines for a student
     */
    private function calculateTotalOutstandingFines($studentId)
    {
        $fineCalculator = new DynamicFineCalculator();
        $overdueBooks = BookIssue::where('student_id', $studentId)
            ->where('issue_status', 'N')
            ->where('return_date', '<', now())
            ->get();

        $totalFine = 0;
        foreach ($overdueBooks as $book) {
            $fineData = $fineCalculator->calculateProgressiveFine(
                $book->issue_date,
                $book->return_date
            );
            $totalFine += $fineData['fine_amount'];
        }

        return $totalFine;
    }

    /**
     * Get risk level based on score
     */
    private function getRiskLevel($score)
    {
        if ($score >= 80) {
            return 'critical';
        }
        if ($score >= 50) {
            return 'high';
        }
        if ($score >= 25) {
            return 'medium';
        }
        if ($score > 0) {
            return 'low';
        }
        return 'minimal';
    }

    /**
     * Bulk fraud detection for all students
     */
    public function bulkFraudDetection($limit = 50)
    {
        $students = Student::latest()->take($limit)->get();
        $results = [];

        foreach ($students as $student) {
            $anomalies = $this->detectStudentAnomalies($student->id);
            if ($anomalies['risk_level'] !== 'minimal') {
                $results[] = array_merge($anomalies, [
                    'student_name' => $student->name,
                    'student_email' => $student->email
                ]);
            }
        }

        // Sort by risk score (highest first)
        usort($results, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return $results;
    }
}
