<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CheckOverdueBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue books and send notifications';

    /**
     * The notification service instance.
     *
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for overdue books...');
        
        // Check and create overdue notifications
        $overdueCount = $this->notificationService->checkOverdueBooks();
        $this->info("Created {$overdueCount} overdue notifications.");
        
        // Send pending notifications
        $sentCount = $this->notificationService->sendPendingNotifications();
        $this->info("Sent {$sentCount} notifications.");
        
        $this->info('Overdue book check completed successfully!');
        
        return 0;
    }
}
