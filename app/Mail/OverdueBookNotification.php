<?php

namespace App\Mail;

use App\Models\BookIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OverdueBookNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bookIssue;
    public $overdueDays;
    public $fineAmount;

    /**
     * Create a new message instance.
     */
    public function __construct(BookIssue $bookIssue, int $overdueDays, float $fineAmount)
    {
        $this->bookIssue = $bookIssue;
        $this->overdueDays = $overdueDays;
        $this->fineAmount = $fineAmount;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Overdue Book Notice - Library Management System')
                    ->view('emails.overdue-book-notification')
                    ->with([
                        'student' => $this->bookIssue->student,
                        'book' => $this->bookIssue->book,
                        'overdueDays' => $this->overdueDays,
                        'fineAmount' => $this->fineAmount,
                        'issueDate' => $this->bookIssue->issue_date,
                        'returnDate' => $this->bookIssue->return_date,
                    ]);
    }
}
