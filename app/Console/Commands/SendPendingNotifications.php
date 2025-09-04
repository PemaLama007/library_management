<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class SendPendingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all pending notifications';

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
        $this->info('Sending pending notifications...');
        
        // Send pending notifications
        $sentCount = $this->notificationService->sendPendingNotifications();
        $this->info("Sent {$sentCount} notifications successfully.");
        
        $this->info('Notification sending completed!');
        
        return 0;
    }
}
