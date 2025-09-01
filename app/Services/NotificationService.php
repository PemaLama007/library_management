<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\BookIssue;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create due date reminder notification
     */
    public function createDueReminder(BookIssue $bookIssue, int $daysBefore = 2): Notification
    {
        $dueDate = Carbon::parse($bookIssue->return_date);
        $scheduledAt = $dueDate->subDays($daysBefore);
        
        return Notification::create([
            'student_id' => $bookIssue->student_id,
            'book_issue_id' => $bookIssue->id,
            'type' => 'due_reminder',
            'title' => 'Book Due Reminder',
            'message' => "Dear {$bookIssue->student->name}, your book '{$bookIssue->book->name}' is due on {$dueDate->format('M d, Y')}. Please return it on time to avoid fines.",
            'scheduled_at' => $scheduledAt,
            'status' => 'pending'
        ]);
    }

    /**
     * Create overdue notice notification
     */
    public function createOverdueNotice(BookIssue $bookIssue): Notification
    {
        $overdueDays = Carbon::now()->diffInDays(Carbon::parse($bookIssue->return_date));
        $fine = $this->calculateFine($bookIssue);
        
        return Notification::create([
            'student_id' => $bookIssue->student_id,
            'book_issue_id' => $bookIssue->id,
            'type' => 'overdue_notice',
            'title' => 'Overdue Book Notice',
            'message' => "Dear {$bookIssue->student->name}, your book '{$bookIssue->book->name}' is {$overdueDays} day(s) overdue. Current fine: \${$fine}. Please return immediately.",
            'scheduled_at' => Carbon::now(),
            'status' => 'pending'
        ]);
    }

    /**
     * Create return confirmation notification
     */
    public function createReturnConfirmation(BookIssue $bookIssue): Notification
    {
        return Notification::create([
            'student_id' => $bookIssue->student_id,
            'book_issue_id' => $bookIssue->id,
            'type' => 'return_confirmation',
            'title' => 'Book Return Confirmed',
            'message' => "Dear {$bookIssue->student->name}, thank you for returning '{$bookIssue->book->name}' on time.",
            'scheduled_at' => Carbon::now(),
            'status' => 'pending'
        ]);
    }

    /**
     * Send pending notifications
     */
    public function sendPendingNotifications(): int
    {
        $notifications = Notification::where('status', 'pending')
            ->where('scheduled_at', '<=', Carbon::now())
            ->with(['student', 'bookIssue.book'])
            ->get();

        $sentCount = 0;
        foreach ($notifications as $notification) {
            if ($this->sendNotification($notification)) {
                $notification->update([
                    'status' => 'sent',
                    'sent_at' => Carbon::now()
                ]);
                $sentCount++;
            } else {
                $notification->update(['status' => 'failed']);
            }
        }

        return $sentCount;
    }

    /**
     * Send individual notification
     */
    private function sendNotification(Notification $notification): bool
    {
        // For now, we'll just mark as sent
        // In the future, integrate with email/SMS services
        
        // Example email integration (uncomment when needed):
        // Mail::to($notification->student->email)->send(new NotificationMail($notification));
        
        // Simulate successful sending
        Log::info("Notification sent: {$notification->title} to {$notification->student->name}");
        
        return true;
    }

    /**
     * Calculate fine for overdue book
     */
    private function calculateFine(BookIssue $bookIssue): float
    {
        // Use DynamicFineCalculator for progressive fine
        $fineCalculator = new \App\Services\DynamicFineCalculator();
        $fineData = $fineCalculator->calculateProgressiveFine(
            $bookIssue->issue_date,
            $bookIssue->return_date,
            $bookIssue->actual_return_date ?? now()->format('Y-m-d')
        );
        return $fineData['fine_amount'] ?? 0;
    }

    /**
     * Schedule notifications for new book issue
     */
    public function scheduleNotificationsForBookIssue(BookIssue $bookIssue): void
    {
        // Schedule reminder 2 days before due date
        $this->createDueReminder($bookIssue, 2);
        
        // Schedule reminder 1 day before due date
        $this->createDueReminder($bookIssue, 1);
    }

    /**
     * Check and create overdue notifications
     */
    public function checkOverdueBooks(): int
    {
        $overdueIssues = BookIssue::where('issue_status', 'N')
            ->where('return_date', '<', Carbon::now()->format('Y-m-d'))
            ->with(['student', 'book'])
            ->get();

        $notificationCount = 0;
        foreach ($overdueIssues as $issue) {
            // Check if overdue notification already exists for today
            $existingNotification = Notification::where('book_issue_id', $issue->id)
                ->where('type', 'overdue_notice')
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$existingNotification) {
                $this->createOverdueNotice($issue);
                $notificationCount++;
            }
        }

        return $notificationCount;
    }
}
