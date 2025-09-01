<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Settings;

class DynamicFineCalculator
{
    /**
     * Calculate progressive fine with tiered rates
     * 
     * @param string $issueDate
     * @param string|null $returnDate  
     * @param string|null $actualReturnDate
     * @return array
     */
    public function calculateProgressiveFine($issueDate, $returnDate, $actualReturnDate = null)
    {
        $actualReturnDate = $actualReturnDate ?? now()->format('Y-m-d');
        $issueDate = Carbon::parse($issueDate);
        $dueDate = Carbon::parse($returnDate);
        $returnedDate = Carbon::parse($actualReturnDate);
        
        // No fine if returned on time
        if ($returnedDate <= $dueDate) {
            return [
                'fine_amount' => 0,
                'overdue_days' => 0,
                'fine_breakdown' => [],
                'status' => 'on_time'
            ];
        }
        
        $overdueDays = $dueDate->diffInDays($returnedDate);
        $fine = 0;
        $breakdown = [];
        $remainingDays = $overdueDays;
        
        // Get base fine rate from settings
        $settings = Settings::latest()->first();
        $baseFineRate = $settings ? $settings->fine : 2; // Default ₹2 if no setting
        
        // Progressive fine structure
        $tiers = [
            ['days' => 3, 'rate' => $baseFineRate, 'label' => 'Grace Period (1-3 days)'],
            ['days' => 7, 'rate' => $baseFineRate * 1.5, 'label' => 'Week 1 (4-10 days)'],
            ['days' => 7, 'rate' => $baseFineRate * 2, 'label' => 'Week 2 (11-17 days)'],
            ['days' => 14, 'rate' => $baseFineRate * 3, 'label' => 'Weeks 3-4 (18-31 days)'],
            ['days' => PHP_INT_MAX, 'rate' => $baseFineRate * 5, 'label' => 'Extended Overdue (32+ days)']
        ];
        
        foreach ($tiers as $tier) {
            if ($remainingDays <= 0) break;
            
            $daysInTier = min($remainingDays, $tier['days']);
            $tierFine = $daysInTier * $tier['rate'];
            
            if ($daysInTier > 0) {
                $breakdown[] = [
                    'tier' => $tier['label'],
                    'days' => $daysInTier,
                    'rate_per_day' => $tier['rate'],
                    'amount' => $tierFine
                ];
                
                $fine += $tierFine;
            }
            
            $remainingDays -= $daysInTier;
        }
        
        // Apply late return penalty for very long overdue (30+ days)
        if ($overdueDays > 30) {
            $latePenalty = $baseFineRate * 10; // Fixed penalty
            $fine += $latePenalty;
            $breakdown[] = [
                'tier' => 'Late Return Penalty',
                'days' => 1,
                'rate_per_day' => $latePenalty,
                'amount' => $latePenalty
            ];
        }
        
        return [
            'fine_amount' => round($fine, 2),
            'overdue_days' => $overdueDays,
            'fine_breakdown' => $breakdown,
            'status' => $this->getFineStatus($overdueDays),
            'severity' => $this->getFineSeverity($fine, $baseFineRate)
        ];
    }
    
    /**
     * Calculate fine for multiple books (bulk calculation)
     */
    public function calculateBulkFines($bookIssues)
    {
        $totalFine = 0;
        $fineDetails = [];
        
        foreach ($bookIssues as $issue) {
            $fineData = $this->calculateProgressiveFine(
                $issue->issue_date,
                $issue->return_date,
                $issue->actual_return_date ?? now()->format('Y-m-d')
            );
            
            $fineDetails[] = [
                'book_issue_id' => $issue->id,
                'book_name' => $issue->book->name ?? 'Unknown',
                'student_name' => $issue->student->name ?? 'Unknown',
                'fine_data' => $fineData
            ];
            
            $totalFine += $fineData['fine_amount'];
        }
        
        return [
            'total_fine' => $totalFine,
            'total_books' => count($bookIssues),
            'fine_details' => $fineDetails
        ];
    }
    
    /**
     * Get fine status based on overdue days
     */
    private function getFineStatus($days)
    {
        if ($days <= 3) return 'minimal';
        if ($days <= 10) return 'moderate';
        if ($days <= 30) return 'high';
        return 'critical';
    }
    
    /**
     * Get fine severity level
     */
    private function getFineSeverity($fine, $baseFine)
    {
        $ratio = $fine / $baseFine;
        
        if ($ratio <= 5) return 'low';
        if ($ratio <= 15) return 'medium';
        if ($ratio <= 30) return 'high';
        return 'severe';
    }
    
    /**
     * Calculate fine remission/discount for good behavior
     */
    public function calculateFineRemission($studentId, $currentFine)
    {
        // Check student's history for good behavior
        $student = \App\Models\Student::find($studentId);
        $recentIssues = $student->bookIssues()
            ->where('created_at', '>=', now()->subMonths(6))
            ->get();
            
        $onTimeReturns = $recentIssues->filter(function($issue) {
            return $issue->return_date && $issue->actual_return_date && 
                   Carbon::parse($issue->actual_return_date) <= Carbon::parse($issue->return_date);
        })->count();
        
        $totalIssues = $recentIssues->count();
        
        if ($totalIssues >= 5) {
            $onTimePercentage = ($onTimeReturns / $totalIssues) * 100;
            
            // Apply discount based on good behavior
            if ($onTimePercentage >= 90) {
                $discount = $currentFine * 0.5; // 50% discount
                return [
                    'discount_percentage' => 50,
                    'discount_amount' => $discount,
                    'final_fine' => $currentFine - $discount,
                    'reason' => 'Excellent return history (90%+ on-time)'
                ];
            } elseif ($onTimePercentage >= 75) {
                $discount = $currentFine * 0.25; // 25% discount
                return [
                    'discount_percentage' => 25,
                    'discount_amount' => $discount,
                    'final_fine' => $currentFine - $discount,
                    'reason' => 'Good return history (75%+ on-time)'
                ];
            }
        }
        
        return [
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'final_fine' => $currentFine,
            'reason' => 'No discount applicable'
        ];
    }
}
