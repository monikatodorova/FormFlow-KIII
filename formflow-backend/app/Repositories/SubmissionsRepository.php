<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Form;
use App\Models\Submission;

class SubmissionsRepository {

    public const RESULTS_PER_PAGE = 20;

    /**
     * Gets all submissions for the provided form, and filters them by their status if needed.
     * @param Form $form
     * @param null $submissionStatus
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public static function getAllSubmissionsForForm(Form $form, $submissionStatus = null, $page = 0, $perPage = self::RESULTS_PER_PAGE) {
        $leads = Submission::query();
        $leads->where('form_id', '=', $form->getId());
        if($submissionStatus && in_array($submissionStatus, Submission::STATUS_OPTIONS)) {
            $leads->where('status', '=', $submissionStatus);
        }
        $totalLeads = $leads->count();
        $leads->orderByDesc('id');

        if((!is_numeric($perPage) && strtolower($perPage) !== 'all') || $perPage <= 0) {
            $perPage = self::RESULTS_PER_PAGE;
        }

        if(strtolower($perPage) === 'all') {
            $items = $leads->get();
        } else {
            $pagination = $leads->cursorPaginate($perPage);
            $items = $pagination->items();
        }

        return [
            'items' => $items,
            'cursor' => isset($pagination) ? ($pagination->nextCursor() ? $pagination->nextCursor()->encode() : null) : null,
            'total' => $totalLeads,
        ];
    }

    /**
     * Gets all submissions for the provided project, and filters them by their status if needed.
     * @param Project $project
     * @param $submissionStatus
     * @param $page
     * @return array
     */
    public static function getAllSubmissionsForProject(Project $project, $submissionStatus = null, $page = 0) {
        $leads = Submission::query();
        $leads->with(['tags', 'form']);
        $leads->whereIn('form_id', FormsRepository::getAllFormIdsForProject($project));
        if($submissionStatus && in_array($submissionStatus, Submission::STATUS_OPTIONS)) {
            $leads->where('status', '=', $submissionStatus);
        }
        $totalLeads = $leads->count();
        $leads->orderByDesc('id');
        $pagination = $leads->cursorPaginate(self::RESULTS_PER_PAGE);
        return [
            'items' => $pagination->items(),
            'cursor' => $pagination->nextCursor() ? $pagination->nextCursor()->encode() : null,
            'total' =>$totalLeads,
        ];
    }

    /**
     * Sets the value to the provided attribute on the submission.
     * @param Submission $submission
     * @param string $attribute
     * @param $value
     * @return void
     */
    public static function updateAttribute(Submission $submission, string $attribute, $value) {
        $submission->setAttribute($attribute, $value);
        $submission->save();
    }

    /**
     * Total number of submissions for project on given date
     * @param Project $project
     * @param $date
     * @return int
     */
    public static function getTotalSubmissionsForProjectOnDate(Project $project, $date)
    {
        return Submission::query()->whereIn('form_id', FormsRepository::getAllFormIdsForProject($project))->whereDate('created_at', '=', $date)->count();
    }

    /**
     * Saves the given tag to the given submission.
     * @param Submission $submission
     * @param \Illuminate\Database\Eloquent\Model $tag
     * @return void
     */
    public static function addTagToSubmission(Submission $submission, \Illuminate\Database\Eloquent\Model $tag) {
        $submission->tags()->attach($tag);
        $submission->save();
    }

    /**
     * Deletes the given tag from the given submission.
     * @param Submission $submission
     * @param \Illuminate\Database\Eloquent\Model $tag
     * @return void
     */
    public static function deleteTagFromSubmission(Submission $submission, \Illuminate\Database\Eloquent\Model $tag) {
        $submission->tags()->detach($tag);
        $submission->save();
    }

    /**
     * Returns the total amount of submissions during a given period for the provided project.
     * @param Project $project
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public static function getTotalSubmissionsForProjectForPeriod(Project $project, $startDate, $endDate) {
        return Submission::query()
            ->whereIn('form_id', FormsRepository::getAllFormIdsForProject($project))
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();
    }

    /**
     * Returns the total amount of submissions during a given period for the provided form.
     * @param Form $form
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public static function getTotalSubmissionsForFormForPeriod(Form $form, $startDate, $endDate) {
        return Submission::query()
            ->where('form_id', '=', $form->id)
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->count();
    }

    /**
     * Returns the total amount of submissions for the provided project.
     * @param Project $project
     * @param null $submissionStatus
     * @return int
     */
    public static function getTotalSubmissionsForProject(Project $project, $submissionStatus = null) {
        $submissions = Submission::query();
        $submissions->whereIn('form_id', FormsRepository::getAllFormIdsForProject($project));
        if($submissionStatus) {
            $submissions->where('status', '=', $submissionStatus);
        }
        return $submissions->count();
    }

    /**
     * Returns the total amount of submission on a given date for the provided form.
     * @param Form $form
     * @param $date
     * @return int
     */
    public static function getTotalSubmissionsForFormOnDate(Form $form, $date) {
        return $form->submissions()
            ->whereDate('created_at', '=', $date)
            ->count();
    }

    /**
     *
     * @param Form $form
     * @param array $details
     * @param string|null $name
     * @param string|null $email
     * @return mixed
     */
    public static function saveSubmission(Form $form, array $details, string $name = null, string $email = null) {
        $details = self::prepareFields($details);
        return Submission::create([
            'name' => $name,
            'email' => $email,
            'fields' => $details,
            'form_id' => $form->id,
            'status' => 'new',
        ]);
    }

    /**
     * Prepare the fields for saving them in the database.
     * Skip any unwanted key/value combinations.
     * @param array $details
     * @return array
     */
    private static function prepareFields(array $details) {
        $fields = [];
        $details = array_slice($details, 0, Submission::FIELDS_LIMIT);
        foreach($details as $key => $value) {
            $fields[self::prepareKey($key)] = self::prepareValue($value);
        }
        return $fields;
    }

    /**
     * Filters the provided string from any unexpected characters.
     * @param $key
     * @return array|string|string[]|null
     */
    private static function prepareKey($key) {
        // Strip any tags
        $key = strip_tags($key);

        // Remove any lines from name
        $key = preg_replace("/[-_]/", ' ', $key);

        // Remove any non-alphanumeric characters
        $key = preg_replace("/[^A-Za-z0-9 ]/", '', $key);

        // Check if the key contains any characters
        if(strlen($key) === 0) return "N/A";

        // Return key otherwise
        return ucwords($key);
    }

    /**
     * Filters the provided string from any unexpected values.
     * @param $value
     * @return string
     */
    private static function prepareValue($value) {
        return strip_tags($value);
    }

    /**
     * Returns the total amount of submissions for the provided form.
     * @param Form $form
     * @param $submissionStatus
     * @return int
     */
    public static function getTotalSubmissionsForForm(Form $form, $submissionStatus = null) {
        if(!$submissionStatus) return $form->submissions()->count();
        return $form->submissions()->where('status', '=', $submissionStatus)->count();
    }

}
