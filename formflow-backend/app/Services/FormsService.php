<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Form;
use App\Models\Recipient;
use App\Models\Submission;
use App\Repositories\FormsRepository;
use App\Repositories\ColorsRepository;
use App\Repositories\NotificationsRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormsService {

    /**
     * Returns all projects for current user.
     * @return Response
     */
    public static function getAllFormsForProject(Project $project) {
        return new Response([
            'status' => 'success',
            'forms' => FormsRepository::getAllFormsForProject($project),
        ], 200);
    }

    /**
     * Creates new form with the provided details.
     * @param Project $project
     * @param array $details
     * @param bool $returnObject
     * @return Response | Form
     */
    public static function createForm(Project $project, array $details, bool $returnObject = false) {
        try {
            self::validateRequiredFields($details);
        } catch (ValidationException $e) {
            if($returnObject) return null;
            return new Response([
                'status' => 'error',
                'message' => $e->getResponse(),
            ], 400);
        }

        $color = ColorsRepository::getColor($details['color_id'] ?? null);
        $form = FormsRepository::createForm($project, $color, [
            'name' => $details['name'],
        ]);

        Recipient::create([
            'email' => 'test@email.com',
            'vefiried' => 1,
            'form_id' => $form->getId(),
        ]);

        if($returnObject) return $form;
        return new Response([
            'status' => 'success',
            'form' => FormsRepository::details($form),
        ]);
    }

     /**
     * Provides the details for the current form.
     * @param Form $form
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public static function details(Form $form) {
        return FormsRepository::details($form);
    }

    /**
     * Updates the provided attributes for the form.
     * @param Form $form
     * @param array $details
     * @return Response
     */
    public static function updateForm(Form $form, array $details) {
        if(isset($details['name'])) FormsRepository::updateAttribute($form, 'name', $details['name']);
        if(isset($details['color_id'])) FormsRepository::updateAttribute($form, 'color_id', ColorsRepository::getColor($details['color_id'])->getId());

        return new Response([
            'status' => 'success',
            'form' => FormsRepository::details($form),
        ]);
    }

     /**
     * Deletes the provided form.
     * @param Form $form
     * @return Response
     */
    public static function delete(Form $form) {
        FormsRepository::delete($form);
        return new Response([
            'status' => 'success',
            'message' => 'Form has been deleted',
        ]);
    }

    /**
     * Validates the required fields for new form.
     * @throws ValidationException
     */
    private static function validateRequiredFields($details) {
        $validator = Validator::make($details, [
            'name' => 'required',
        ]);
        if($validator->fails()) throw new ValidationException($validator, "Missing required fields.", $validator->errors());
        return true;
    }

    /**
     * Notifies all form recipients about the new submission.
     * @param Submission $submission
     * @return void
     */
    public static function notifyRecipients(Submission $submission) {
        foreach($submission->form->recipients as $recipient) {
            NotificationsRepository::notifyFormRecipientForSubmission($recipient, $submission);
        }
    }


}