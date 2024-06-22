<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Project;
use App\Services\FormsService;
use App\Services\SubmissionsService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class FormsController extends Controller
{
    public function index(Project $project) {
        return FormsService::getAllFormsForProject($project);
    }

    public function store(Project $project, Request $request) {
        return FormsService::createForm($project, $request->all());
    }

    public function details(Project $project, Form $form) {
        return FormsService::details($form);
    }

    public function update(Project $project, Form $form, Request $request) {
        return FormsService::updateForm($form, $request->all());
    }

    public function delete(Project $project, Form $form) {
        return FormsService::delete($form);
    }

    public function submissions(Project $project, Form $form, Request $request) {
        return SubmissionsService::getAllSubmissionsForForm($form, $request->get('status'), $request->get('page'), $request->get('perPage'));
    }

    public function statistics(Project $project, Form $form, Request $request) {
        return StatisticsService::getStatisticsForForm($form, $request);
    }
}
