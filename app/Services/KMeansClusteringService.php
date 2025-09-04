<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * K-Means Clustering Service for Library Data Analysis
 * Provides intelligent clustering of students, books, and borrowing patterns
 */
class KMeansClusteringService
{
    private $maxIterations = 100;
    private $tolerance = 0.001;

    /**
     * Cluster students by borrowing behavior
     * Features: Total books borrowed, overdue rate, fine amount, reading diversity
     */
    public function clusterStudentsByBehavior(int $k = 3): array
    {
        $students = Student::with(['bookIssues.book.category'])->get();
        $features = [];

        foreach ($students as $student) {
            $features[] = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'total_borrowed' => $student->bookIssues->count(),
                'overdue_rate' => $this->calculateOverdueRate($student),
                'total_fines' => $this->calculateTotalFines($student),
                'category_diversity' => $this->calculateCategoryDiversity($student),
                'avg_reading_speed' => $this->calculateReadingSpeed($student),
                'return_compliance' => $this->calculateReturnCompliance($student)
            ];
        }

        // Normalize features for clustering
        $normalizedFeatures = $this->normalizeFeatures($features);
        
        // Perform K-means clustering
        $clusters = $this->performKMeans($normalizedFeatures, $k);
        
        // Map results back to students
        return $this->mapStudentClusters($students, $clusters, $features);
    }

    /**
     * Cluster books by popularity and usage patterns
     * Features: Borrowing frequency, category, author popularity, availability
     */
    public function clusterBooksByUsage(int $k = 4): array
    {
        $books = Book::with(['author', 'category', 'bookIssues'])
            ->withCount('bookIssues')
            ->get();

        $features = [];
        foreach ($books as $book) {
            $features[] = [
                'book_id' => $book->id,
                'book_name' => $book->name,
                'borrow_count' => $book->book_issues_count,
                'category_id' => $book->category_id,
                'author_id' => $book->author_id,
                'availability_rate' => $book->available_copies / max($book->total_copies, 1),
                'avg_borrow_duration' => $this->calculateAvgBorrowDuration($book),
                'recent_popularity' => $this->calculateRecentPopularity($book)
            ];
        }

        $normalizedFeatures = $this->normalizeFeatures($features);
        $clusters = $this->performKMeans($normalizedFeatures, $k);
        
        return $this->mapBookClusters($books, $clusters, $features);
    }

    /**
     * Cluster borrowing patterns by time and behavior
     * Features: Issue frequency, return patterns, seasonal trends
     */
    public function clusterBorrowingPatterns(int $k = 3): array
    {
        $issues = BookIssue::with(['student', 'book'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy('student_id');

        $features = [];
        foreach ($issues as $studentId => $studentIssues) {
            $features[] = [
                'student_id' => $studentId,
                'student_name' => $studentIssues->first()->student->name,
                'monthly_frequency' => $this->calculateMonthlyFrequency($studentIssues),
                'weekday_preference' => $this->calculateWeekdayPreference($studentIssues),
                'return_timing' => $this->calculateReturnTiming($studentIssues),
                'book_category_preference' => $this->calculateCategoryPreference($studentIssues),
                'seasonal_pattern' => $this->calculateSeasonalPattern($studentIssues)
            ];
        }

        $normalizedFeatures = $this->normalizeFeatures($features);
        $clusters = $this->performKMeans($normalizedFeatures, $k);
        
        return $this->mapBorrowingClusters($issues, $clusters, $features);
    }

    /**
     * Perform K-means clustering algorithm
     */
    private function performKMeans(array $data, int $k): array
    {
        $n = count($data);
        if ($n < $k) {
            $k = $n;
        }

        // Initialize centroids randomly
        $centroids = $this->initializeCentroids($data, $k);
        $clusters = array_fill(0, $n, 0);
        $iteration = 0;

        do {
            $oldClusters = $clusters;
            
            // Assign points to nearest centroid
            for ($i = 0; $i < $n; $i++) {
                $clusters[$i] = $this->findNearestCentroid($data[$i], $centroids);
            }
            
            // Update centroids
            $centroids = $this->updateCentroids($data, $clusters, $k);
            
            $iteration++;
        } while ($iteration < $this->maxIterations && 
                 $this->calculateClusterChange($oldClusters, $clusters) > $this->tolerance);

        return [
            'clusters' => $clusters,
            'centroids' => $centroids,
            'iterations' => $iteration,
            'converged' => $iteration < $this->maxIterations
        ];
    }

    /**
     * Initialize centroids randomly from data points
     */
    private function initializeCentroids(array $data, int $k): array
    {
        $centroids = [];
        $n = count($data);
        $featureCount = count($data[0]) - 2; // Exclude ID and name fields
        
        for ($i = 0; $i < $k; $i++) {
            $randomIndex = rand(0, $n - 1);
            $centroid = [];
            
            // Copy features (skip ID and name)
            for ($j = 2; $j < count($data[$randomIndex]); $j++) {
                $centroid[] = $data[$randomIndex][$j];
            }
            
            $centroids[] = $centroid;
        }
        
        return $centroids;
    }

    /**
     * Find the nearest centroid for a data point
     */
    private function findNearestCentroid(array $point, array $centroids): int
    {
        $minDistance = PHP_FLOAT_MAX;
        $nearestCentroid = 0;
        
        // Extract features (skip ID and name)
        $features = array_slice($point, 2);
        
        foreach ($centroids as $i => $centroid) {
            $distance = $this->calculateEuclideanDistance($features, $centroid);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestCentroid = $i;
            }
        }
        
        return $nearestCentroid;
    }

    /**
     * Calculate Euclidean distance between two points
     */
    private function calculateEuclideanDistance(array $point1, array $point2): float
    {
        $sum = 0;
        for ($i = 0; $i < count($point1); $i++) {
            $sum += pow($point1[$i] - $point2[$i], 2);
        }
        return sqrt($sum);
    }

    /**
     * Update centroids based on current cluster assignments
     */
    private function updateCentroids(array $data, array $clusters, int $k): array
    {
        $centroids = array_fill(0, $k, []);
        $counts = array_fill(0, $k, 0);
        
        // Initialize centroids with zeros
        for ($i = 0; $i < $k; $i++) {
            $featureCount = count($data[0]) - 2; // Exclude ID and name
            $centroids[$i] = array_fill(0, $featureCount, 0);
        }
        
        // Sum up all points in each cluster
        for ($i = 0; $i < count($data); $i++) {
            $cluster = $clusters[$i];
            $counts[$cluster]++;
            
            $features = array_slice($data[$i], 2); // Exclude ID and name
            for ($j = 0; $j < count($features); $j++) {
                $centroids[$cluster][$j] += $features[$j];
            }
        }
        
        // Calculate average (centroid)
        for ($i = 0; $i < $k; $i++) {
            if ($counts[$i] > 0) {
                for ($j = 0; $j < count($centroids[$i]); $j++) {
                    $centroids[$i][$j] /= $counts[$i];
                }
            }
        }
        
        return $centroids;
    }

    /**
     * Calculate how much clusters have changed
     */
    private function calculateClusterChange(array $oldClusters, array $newClusters): float
    {
        $changes = 0;
        for ($i = 0; $i < count($oldClusters); $i++) {
            if ($oldClusters[$i] !== $newClusters[$i]) {
                $changes++;
            }
        }
        return $changes / count($oldClusters);
    }

    /**
     * Normalize features to 0-1 range for better clustering
     */
    private function normalizeFeatures(array $features): array
    {
        if (empty($features)) {
            return [];
        }

        // Convert associative arrays to indexed arrays for processing
        $indexedFeatures = [];
        $keys = array_keys($features[0]);
        $idKey = $keys[0]; // First key is ID
        $nameKey = $keys[1]; // Second key is name
        
        foreach ($features as $feature) {
            $indexedFeature = [$feature[$idKey], $feature[$nameKey]]; // Keep ID and name
            // Add all other features (skip ID and name keys)
            for ($i = 2; $i < count($keys); $i++) {
                $indexedFeature[] = $feature[$keys[$i]];
            }
            $indexedFeatures[] = $indexedFeature;
        }

        $featureCount = count($indexedFeatures[0]) - 2; // Exclude ID and name
        $mins = array_fill(0, $featureCount, PHP_FLOAT_MAX);
        $maxs = array_fill(0, $featureCount, PHP_FLOAT_MIN);

        // Find min and max for each feature
        foreach ($indexedFeatures as $feature) {
            for ($i = 2; $i < count($feature); $i++) {
                $mins[$i - 2] = min($mins[$i - 2], $feature[$i]);
                $maxs[$i - 2] = max($maxs[$i - 2], $feature[$i]);
            }
        }

        // Normalize features
        $normalized = [];
        foreach ($indexedFeatures as $feature) {
            $normalizedFeature = [$feature[0], $feature[1]]; // Keep ID and name
            for ($i = 2; $i < count($feature); $i++) {
                $range = $maxs[$i - 2] - $mins[$i - 2];
                if ($range == 0) {
                    $normalizedFeature[] = 0;
                } else {
                    $normalizedFeature[] = ($feature[$i] - $mins[$i - 2]) / $range;
                }
            }
            $normalized[] = $normalizedFeature;
        }

        return $normalized;
    }

    // Helper methods for feature calculation
    private function calculateOverdueRate($student): float
    {
        $totalIssues = $student->bookIssues->count();
        if ($totalIssues === 0) return 0;
        
        $overdueIssues = $student->bookIssues->filter(function($issue) {
            return $issue->issue_status === 'N' && 
                   now()->gt($issue->return_date);
        })->count();
        
        return $overdueIssues / $totalIssues;
    }

    private function calculateTotalFines($student): float
    {
        $fineCalculator = new DynamicFineCalculator();
        $totalFine = 0;
        
        foreach ($student->bookIssues as $issue) {
            if ($issue->issue_status === 'N' && now()->gt($issue->return_date)) {
                $fineData = $fineCalculator->calculateProgressiveFine(
                    $issue->issue_date,
                    $issue->return_date
                );
                $totalFine += $fineData['fine_amount'];
            }
        }
        
        return $totalFine;
    }

    private function calculateCategoryDiversity($student): float
    {
        $categories = $student->bookIssues->pluck('book.category_id')->unique()->count();
        return $categories / max($student->bookIssues->count(), 1);
    }

    private function calculateReadingSpeed($student): float
    {
        $returnedBooks = $student->bookIssues->where('issue_status', 'Y');
        if ($returnedBooks->count() === 0) return 0;
        
        $totalDays = 0;
        foreach ($returnedBooks as $issue) {
            $totalDays += now()->diffInDays($issue->issue_date);
        }
        
        return $totalDays / $returnedBooks->count();
    }

    private function calculateReturnCompliance($student): float
    {
        $totalIssues = $student->bookIssues->count();
        if ($totalIssues === 0) return 1;
        
        $onTimeReturns = $student->bookIssues->filter(function($issue) {
            return $issue->issue_status === 'Y' && 
                   $issue->actual_return_date <= $issue->return_date;
        })->count();
        
        return $onTimeReturns / $totalIssues;
    }

    private function calculateAvgBorrowDuration($book): float
    {
        $returnedIssues = $book->bookIssues->where('issue_status', 'Y');
        if ($returnedIssues->count() === 0) return 0;
        
        $totalDays = 0;
        foreach ($returnedIssues as $issue) {
            $totalDays += Carbon::parse($issue->issue_date)
                ->diffInDays(Carbon::parse($issue->actual_return_date));
        }
        
        return $totalDays / $returnedIssues->count();
    }

    private function calculateRecentPopularity($book): float
    {
        $recentIssues = $book->bookIssues
            ->where('created_at', '>=', now()->subMonths(3))
            ->count();
        
        return $recentIssues;
    }

    private function calculateMonthlyFrequency($issues): float
    {
        $monthlyCounts = $issues->groupBy(function($issue) {
            return $issue->created_at->format('Y-m');
        })->map->count();
        
        return $monthlyCounts->avg();
    }

    private function calculateWeekdayPreference($issues): float
    {
        $weekdayCounts = $issues->groupBy(function($issue) {
            return $issue->created_at->dayOfWeek;
        })->map->count();
        
        return $weekdayCounts->max() / max($weekdayCounts->sum(), 1);
    }

    private function calculateReturnTiming($issues): float
    {
        $returnedIssues = $issues->where('issue_status', 'Y');
        if ($returnedIssues->count() === 0) return 0;
        
        $totalDays = 0;
        foreach ($returnedIssues as $issue) {
            $totalDays += Carbon::parse($issue->issue_date)
                ->diffInDays(Carbon::parse($issue->actual_return_date));
        }
        
        return $totalDays / $returnedIssues->count();
    }

    private function calculateCategoryPreference($issues): float
    {
        $categories = $issues->pluck('book.category_id')->unique()->count();
        return $categories / max($issues->count(), 1);
    }

    private function calculateSeasonalPattern($issues): float
    {
        $seasonalCounts = $issues->groupBy(function($issue) {
            $month = $issue->created_at->month;
            if (in_array($month, [12, 1, 2])) return 'winter';
            if (in_array($month, [3, 4, 5])) return 'spring';
            if (in_array($month, [6, 7, 8])) return 'summer';
            return 'autumn';
        })->map->count();
        
        return $seasonalCounts->max() / max($seasonalCounts->sum(), 1);
    }

    // Mapping methods for clustering results
    private function mapStudentClusters($students, $clusters, $features): array
    {
        $result = [];
        for ($i = 0; $i < count($students); $i++) {
            $clusterId = $clusters['clusters'][$i];
            if (!isset($result[$clusterId])) {
                $result[$clusterId] = [
                    'cluster_id' => $clusterId,
                    'students' => [],
                    'characteristics' => $this->analyzeStudentCluster($features, $clusters['clusters'], $clusterId)
                ];
            }
            
            $result[$clusterId]['students'][] = [
                'student' => $students[$i],
                'features' => $features[$i]
            ];
        }
        
        return $result;
    }

    private function mapBookClusters($books, $clusters, $features): array
    {
        $result = [];
        for ($i = 0; $i < count($books); $i++) {
            $clusterId = $clusters['clusters'][$i];
            if (!isset($result[$clusterId])) {
                $result[$clusterId] = [
                    'cluster_id' => $clusterId,
                    'books' => [],
                    'characteristics' => $this->analyzeBookCluster($features, $clusters['clusters'], $clusterId)
                ];
            }
            
            $result[$clusterId]['books'][] = [
                'book' => $books[$i],
                'features' => $features[$i]
            ];
        }
        
        return $result;
    }

    private function mapBorrowingClusters($issues, $clusters, $features): array
    {
        $result = [];
        for ($i = 0; $i < count($features); $i++) {
            $clusterId = $clusters['clusters'][$i];
            if (!isset($result[$clusterId])) {
                $result[$clusterId] = [
                    'cluster_id' => $clusterId,
                    'patterns' => [],
                    'characteristics' => $this->analyzeBorrowingCluster($features, $clusters['clusters'], $clusterId)
                ];
            }
            
            $result[$clusterId]['patterns'][] = [
                'student_issues' => $issues[$features[$i]['student_id']],
                'features' => $features[$i]
            ];
        }
        
        return $result;
    }

    // Analysis methods for cluster characteristics
    private function analyzeStudentCluster($features, $clusterAssignments, $clusterId): array
    {
        $clusterFeatures = array_filter($features, function($feature, $index) use ($clusterAssignments, $clusterId) {
            return $clusterAssignments[$index] === $clusterId;
        }, ARRAY_FILTER_USE_BOTH);
        
        $clusterFeatures = array_values($clusterFeatures);
        
        return [
            'size' => count($clusterFeatures),
            'avg_borrowed' => $this->calculateAverage($clusterFeatures, 'total_borrowed'),
            'avg_overdue_rate' => $this->calculateAverage($clusterFeatures, 'overdue_rate'),
            'avg_fines' => $this->calculateAverage($clusterFeatures, 'total_fines'),
            'avg_diversity' => $this->calculateAverage($clusterFeatures, 'category_diversity'),
            'avg_compliance' => $this->calculateAverage($clusterFeatures, 'return_compliance')
        ];
    }

    private function analyzeBookCluster($features, $clusterAssignments, $clusterId): array
    {
        $clusterFeatures = array_filter($features, function($feature, $index) use ($clusterAssignments, $clusterId) {
            return $clusterAssignments[$index] === $clusterId;
        }, ARRAY_FILTER_USE_BOTH);
        
        $clusterFeatures = array_values($clusterFeatures);
        
        return [
            'size' => count($clusterFeatures),
            'avg_borrow_count' => $this->calculateAverage($clusterFeatures, 'borrow_count'),
            'avg_availability' => $this->calculateAverage($clusterFeatures, 'availability_rate'),
            'avg_duration' => $this->calculateAverage($clusterFeatures, 'avg_borrow_duration'),
            'avg_recent_popularity' => $this->calculateAverage($clusterFeatures, 'recent_popularity')
        ];
    }

    private function analyzeBorrowingCluster($features, $clusterAssignments, $clusterId): array
    {
        $clusterFeatures = array_filter($features, function($feature, $index) use ($clusterAssignments, $clusterId) {
            return $clusterAssignments[$index] === $clusterId;
        }, ARRAY_FILTER_USE_BOTH);
        
        $clusterFeatures = array_values($clusterFeatures);
        
        return [
            'size' => count($clusterFeatures),
            'avg_frequency' => $this->calculateAverage($clusterFeatures, 'monthly_frequency'),
            'avg_weekday_preference' => $this->calculateAverage($clusterFeatures, 'weekday_preference'),
            'avg_return_timing' => $this->calculateAverage($clusterFeatures, 'return_timing'),
            'avg_category_preference' => $this->calculateAverage($clusterFeatures, 'book_category_preference')
        ];
    }

    private function calculateAverage($features, $field): float
    {
        $sum = 0;
        $count = 0;
        
        foreach ($features as $feature) {
            if (isset($feature[$field])) {
                $sum += $feature[$field];
                $count++;
            }
        }
        
        return $count > 0 ? $sum / $count : 0;
    }
}
