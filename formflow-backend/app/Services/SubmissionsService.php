<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Form;
use App\Models\Submission;
use App\Models\Tag;
use App\Repositories\SubmissionsRepository;
use App\Repositories\TagsRepository;
use App\Repositories\UsersRepository;
use App\Repositories\FormsRepository;
use Illuminate\Http\Response;

class SubmissionsService {

    /**
     * Returns paginated submissions for forms that are under the provided form.
     * @param Form $form
     * @param null $submissionStatus
     * @param int $page
     * @param int $perPage
     * @return \App\Http\Resources\SubmissionsCollection
     */
    public static function getAllSubmissionsForForm(Form $form, $submissionStatus = null, $page = 0, $perPage = 20) {
        return SubmissionsRepository::getAllSubmissionsForForm($form, $submissionStatus, $page, $perPage);
    }

    /**
     * Returns paginated submissions for forms that are under the provided form.
     * @param Project $project
     * @param null $submissionStatus
     * @param int $page
     * @param int $perPage
     * @return \App\Http\Resources\SubmissionsCollection
     */
    public static function getAllSubmissionsForProject(Project $project, $submissionStatus = null, $page = 0, $perPage = 20) {
        return SubmissionsRepository::getAllSubmissionsForProject($project, $submissionStatus, $page, $perPage);
    }

    /**
     * Returns the details for the provided submission.
     * @param Submission $submission
     * @return Submission
     */
    public static function details(Submission $submission) {
        if($submission->status === 'new') {
            self::updateSubmission($submission, [
                'status' => 'seen',
            ]);
        }
        $submission->tags->makeHidden(['pivot']);
        $submission->form->makeHidden(['total_submissions', 'new_submissions', 'form']);

        return $submission;
    }

    /**
     * Updates the provided attributes on the given submission.
     * @param Submission $submission
     * @param array $details
     * @return Response
     */
    public static function updateSubmission(Submission $submission, array $details) {
        if(isset($details['name'])) SubmissionsRepository::updateAttribute($submission, 'name', $details['name']);
        if(isset($details['email'])) SubmissionsRepository::updateAttribute($submission, 'email', $details['email']);
        if(isset($details['status']) && in_array($details['status'], Submission::STATUS_OPTIONS)) SubmissionsRepository::updateAttribute($submission, 'status', $details['status']);
        
        return new Response([
            'status' => 'success',
            'message' => 'Submissions has been updated',
            'submission' => $submission,
        ]);
    }

    /**
     * Saves the provided tag to the provided submission.
     * @param Submission $submission
     * @param $tagId
     * @return Response
     */
    public static function addTag(Submission $submission, $tagId) {
        $tag = TagsRepository::getTagByIdAndUser($tagId, UsersRepository::getCurrentUser());
        if(!$tag) {
            return new Response([
                'status' => 'error',
                'message' => 'Tag not found.',
            ], 404);
        }
        SubmissionsRepository::addTagToSubmission($submission, $tag);

        return new Response([
            'status' => 'success',
            'message' => 'OK',
        ]);
    }

    /**
     * Removes the provided tag from the provided submission.
     * @param Submission $submission
     * @param $tagId
     * @return Response
     */
    public static function removeTag(Submission $submission, Tag $tag) {
        SubmissionsRepository::deleteTagFromSubmission($submission, $tag);
        return new Response([
            'status' => 'success',
            'message' => 'OK',
        ]);
    }

    /**
     * Stores the provided submission details to the form with this secret key.
     * @param $formSecret
     * @param array $details
     */
    public static function saveSubmission($formSecret, array $details) {
        // Verify form existence
        $form = FormsRepository::getFormBySecretKey($formSecret);
        if(!$form) {
            return new Response([
                'status' => 'error',
                'message' => 'This form does not exist',
            ], 404);
        }

        // Verify input
        if(count($details) === 0) {
            return new Response([
                'status' => 'error',
                'message' => 'The submission does not contain any input fields',
            ], 400);
        }

        // Save submission & notify recipients
        $submission = SubmissionsRepository::saveSubmission($form, $details, self::getSubmissionName($details), self::getSubmissionEmail($details));
        FormsService::notifyRecipients($submission);

        // Response
        return new Response([
            'status' => 'success',
            'message' => 'Submission has been saved',
        ]);
    }

    /**
     * Generates random demo submissions for the provided form
     * @param Form $form
     * @param int $totalSubmissions
     * @return void
     */
    public static function generateDemoSubmissions(Form $form, int $totalSubmissions = 10) {
        // Generate 10 random submissions
        Submission::factory()->count($totalSubmissions)->create([
            'form_id' => $form->id,
        ]);
    }

    /**
     * Try to find a person name in the details that were sent in the submission.
     * @param $details
     * @return string|void
     */
    private static function getSubmissionName($details) {
        $fullNameFields = ['name', 'fullName', 'fullname', 'full-name', 'full_name'];
        foreach($fullNameFields as $field) {
            if(!empty($details[$field])) {
                return mb_convert_case(mb_strtolower(trim($details[$field])), MB_CASE_TITLE);
            }
        }

        $firstNameFields = ['firstname', 'firstName', 'first-name', 'first_name'];
        foreach($firstNameFields as $field) {
            if(!empty($details[$field])) {
                $firstName = mb_convert_case(mb_strtolower(trim($details[$field])), MB_CASE_TITLE);
                break;
            }
        }

        $lastNameFields = ['lastname', 'lastName', 'last-name', 'last_name'];
        foreach($lastNameFields as $field) {
            if(!empty($details[$field])) {
                $lastName = mb_convert_case(mb_strtolower(trim($details[$field])), MB_CASE_TITLE);
                break;
            }
        }

        if(isset($firstName) && isset($lastName)) return $firstName . " " . $lastName;
        elseif(isset($firstName)) return $firstName;
        elseif(isset($lastName)) return $lastName;

        return "Not Available";
    }

    /**
     * Try to find an email in the details that were sent in the submission.
     * @param $details
     * @return string|void
     */
    private static function getSubmissionEmail($details) {
        if(!empty($details['email'])) return trim($details['email']);
        if(!empty($details['eMail'])) return trim($details['eMail']);
        if(!empty($details['e-mail'])) return trim($details['e-mail']);
        if(!empty($details['e_mail'])) return trim($details['e_mail']);
        if(!empty($details['emailAddress'])) return trim($details['emailAddress']);
        if(!empty($details['email-address'])) return trim($details['email-address']);
        if(!empty($details['email_address'])) return trim($details['email_address']);
        if(!empty($details['mail'])) return trim($details['mail']);
        return "Not Available";
    }
}