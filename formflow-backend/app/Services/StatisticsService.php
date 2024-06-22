<?php

namespace App\Services;

use App\Models\Form;
use App\Models\Project;
use App\Models\Submission;
use App\Repositories\FormsRepository;
use App\Repositories\SubmissionsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StatisticsService {

    /**
     * Return statistics for project
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public static function getStatisticsForProject(Project $project, Request $request) {
        $startDate = date("Y-m-d", strtotime("-" . ($request->get('days') ?? 14) . " days"));
        $endDate = date("Y-m-d");

        return new Response([
            "submissions" => self::getSubmissionsStatisticsForProject($project),
            "daily" => self::getSubmissionsBetweenPeriodsForProjectForms($project, $startDate, $endDate),
        ], 200);
    }

    /**
     * Returns statistical breakdown of submissions for the provided project.
     * @param Project $project
     * @return array
     */
    private static function getSubmissionsStatisticsForProject(Project $project) {
        $today = date("Y-m-d");
        $week = date("Y-m-d", strtotime("-7 days"));
        $month = date("Y-m-d", strtotime("-30 days"));

        return [
            'today' => SubmissionsRepository::getTotalSubmissionsForProjectOnDate($project, $today),
            'week' => SubmissionsRepository::getTotalSubmissionsForProjectForPeriod($project, $week, $today),
            'month' => SubmissionsRepository::getTotalSubmissionsForProjectForPeriod($project, $month, $today),
            'total' => SubmissionsRepository::getTotalSubmissionsForProject($project),
        ];
    }

    /**
     * Return the total amount of submissions for each form under the provided project.
     * @param Project $project
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private static function getSubmissionsBetweenPeriodsForProjectForms(Project $project, $startDate, $endDate) {
        $daily = [];
        $forms = FormsRepository::getAllFormsForProject($project);
        foreach($forms as $key => $form) {
            $daily[$key] = [
                'name' => $form->getName(),
                'color' => $form->color->color,
                'submissions' => [],
            ];
            $date = $startDate;
            while($date <= $endDate) {
                $daily[$key]['submissions'][date('F jS', strtotime($date))] = SubmissionsRepository::getTotalSubmissionsForFormOnDate($form, $date);
                $date = date("Y-m-d", strtotime($date . " +1 days"));
            }
        }

        return $daily;
    }

    /**
     * Calculates & returns statistics for submissions for the provided form.
     * @param Form $form
     * @param Request $request
     * @return Response
     */
    public static function getStatisticsForForm(Form $form, Request $request) {
        return new Response(self::getSubmissionsStatisticsForForm($form), 200);
    }

    /**
     * Returns statistical breakdown of submissions for the provided form.
     * @param Form $form
     * @return array
     */
    private static function getSubmissionsStatisticsForForm(Form $form) {
        $submissions = [];
        foreach(Submission::STATUS_OPTIONS as $STATUS_OPTION) {
            $submissions[$STATUS_OPTION] = SubmissionsRepository::getTotalSubmissionsForForm($form, $STATUS_OPTION);
        }
        $submissions['total'] = SubmissionsRepository::getTotalSubmissionsForForm($form);
        $today = date("Y-m-d");
        $week = date("Y-m-d", strtotime("-7 days"));
        $month = date("Y-m-d", strtotime("-30 days"));
        $submissions['today'] = SubmissionsRepository::getTotalSubmissionsForFormOnDate($form, $today);
        $submissions['week'] = SubmissionsRepository::getTotalSubmissionsForFormForPeriod($form, $week, $today);
        $submissions['month'] = SubmissionsRepository::getTotalSubmissionsForFormForPeriod($form, $month, $today);
        $submissions['daily'] = self::getSubmissionsBetweenPeriodsForForm($form, $month, $today);
        $submissions['form'] = $form;

        return $submissions;
    }

    /**
     * Returns the total amount of submissions per day during a given period for the provided form.
     * @param Form $form
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private static function getSubmissionsBetweenPeriodsForForm(Form $form, $startDate, $endDate) {
        $daily = [];
        $date = $startDate;
        
        while($date <= $endDate) {
            $daily[date('F jS', strtotime($date))] = SubmissionsRepository::getTotalSubmissionsForFormOnDate($form, $date);
            $date = date("Y-m-d", strtotime($date . " +1 days"));
        }

        return $daily;
    }

}