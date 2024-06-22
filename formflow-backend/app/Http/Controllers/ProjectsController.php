<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectsService;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    public function index() {
        return ProjectsService::getAllProjects();
    }

    public function create(Request $request) {
        return ProjectsService::create($request->all());
    }

    public function delete(Project $project) {
        return ProjectsService::delete($project);
    }

    public function details(Project $project) {
        return ProjectsService::details($project);
    }

    public function update(Project $project, Request $request) {
        return ProjectsService::updateProject($project, $request->all());
    }

    public function statistics(Project $project, Request $request) {
        return StatisticsService::getStatisticsForProject($project, $request);
    }
}
