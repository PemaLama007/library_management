<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BookIssue;
use App\Mail\OverdueBookNotification;
use Illuminate\Support\Facades\Mail;

class TestEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:test-email {--student-id=} {--book-issue-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notification system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $studentId = $this->option('student-id');
        $bookIssueId = $this->option('book-issue-id');

        if ($bookIssueId) {
            $bookIssue = BookIssue::with(['student', 'book'])->find($bookIssueId);
        } elseif ($studentId) {
            $bookIssue = BookIssue::with(['student', 'book'])
                ->where('student_id', $studentId)
                ->where('issue_status', 'N')
                ->first();
        } else {
            $bookIssue = BookIssue::with(['student', 'book'])
                ->where('issue_status', 'N')
                ->first();
        }

        if (!$bookIssue) {
            $this->error('No book issue found for testing.');
            return 1;
        }

        $this->info("Testing email notification for:");
        $this->info("Student: {$bookIssue->student->name} ({$bookIssue->student->email})");
        $this->info("Book: {$bookIssue->book->name}");
        $this->info("Issue Date: {$bookIssue->issue_date}");
        $this->info("Return Date: {$bookIssue->return_date}");

        $overdueDays = now()->diffInDays($bookIssue->return_date);
        $this->info("Overdue Days: {$overdueDays}");

        try {
            Mail::to($bookIssue->student->email)
                ->send(new OverdueBookNotification($bookIssue, $overdueDays, 5.00));

            $this->info("✅ Test email sent successfully to {$bookIssue->student->email}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
