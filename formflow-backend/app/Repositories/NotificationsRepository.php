<?php

namespace App\Repositories;

use App\Mail\NewFormSubmissionMail;
use App\Models\Recipient;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;

class NotificationsRepository {

    /**
     * Sends email to the recipient about the new form submission.
     * @param Recipient $recipient
     * @param Submission $submission
     * @return void
     */
    public static function notifyFormRecipientForSubmission(Recipient $recipient, Submission $submission) {
        Mail::to($recipient->getEmail())->queue(new NewFormSubmissionMail($submission, $recipient));
    }

}
