<?php

namespace App\Mail;

use App\Models\Recipient;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NewFormSubmissionMail extends Mailable
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $submission;
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Submission $submission, ?Recipient $recipient)
    {
        $this->submission = $submission;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('[New Submission] ' . $this->submission->form->getName())
            ->view('mails.new-submission')
            ->with([
                'submission' => $this->submission,
                'recipient' => $this->recipient,
            ]);
    }
}
