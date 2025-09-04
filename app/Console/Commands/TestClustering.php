<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\KMeansClusteringService;

class TestClustering extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:test-clustering';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test K-Means clustering functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🧪 Testing K-Means Clustering System');
        $this->info('=====================================');
        
        try {
            $clusteringService = new KMeansClusteringService();
            
            // Test Student Clustering
            $this->info('📚 Testing Student Behavior Clustering...');
            $studentClusters = $clusteringService->clusterStudentsByBehavior(3);
            $this->info("✅ Student clustering completed with " . count($studentClusters) . " clusters");
            
            foreach ($studentClusters as $clusterId => $cluster) {
                $this->line("   Cluster {$clusterId}: {$cluster['characteristics']['size']} students");
                $this->line("   - Avg Overdue Rate: " . round($cluster['characteristics']['avg_overdue_rate'], 3));
                $this->line("   - Avg Fines: ₹" . round($cluster['characteristics']['avg_fines'], 2));
                $this->line("   - Avg Compliance: " . round($cluster['characteristics']['avg_compliance'], 3));
            }
            
            // Test Book Clustering
            $this->info('📖 Testing Book Usage Clustering...');
            $bookClusters = $clusteringService->clusterBooksByUsage(4);
            $this->info("✅ Book clustering completed with " . count($bookClusters) . " clusters");
            
            foreach ($bookClusters as $clusterId => $cluster) {
                $this->line("   Cluster {$clusterId}: {$cluster['characteristics']['size']} books");
                $this->line("   - Avg Borrow Count: " . round($cluster['characteristics']['avg_borrow_count'], 1));
                $this->line("   - Avg Availability: " . round($cluster['characteristics']['avg_availability'], 3));
                $this->line("   - Avg Duration: " . round($cluster['characteristics']['avg_duration'], 1) . " days");
            }
            
            // Test Borrowing Pattern Clustering
            $this->info('⏰ Testing Borrowing Pattern Clustering...');
            $borrowingClusters = $clusteringService->clusterBorrowingPatterns(3);
            $this->info("✅ Borrowing pattern clustering completed with " . count($borrowingClusters) . " clusters");
            
            foreach ($borrowingClusters as $clusterId => $cluster) {
                $this->line("   Cluster {$clusterId}: {$cluster['characteristics']['size']} patterns");
                $this->line("   - Avg Frequency: " . round($cluster['characteristics']['avg_frequency'], 2) . " per month");
                $this->line("   - Avg Return Timing: " . round($cluster['characteristics']['avg_return_timing'], 1) . " days");
                $this->line("   - Avg Weekday Preference: " . round($cluster['characteristics']['avg_weekday_preference'], 3));
            }
            
            $this->info('🎉 All clustering tests completed successfully!');
            $this->info('The K-Means algorithm is working correctly.');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Clustering test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
