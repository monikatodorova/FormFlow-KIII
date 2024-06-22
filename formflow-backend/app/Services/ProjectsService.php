<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Http\Response;

class ProjectsService {

    /**
     * Returns all projects for current user.
     * @return Response
     */
    public static function getAllProjects() {
        return new Response([
            'status' => 'success',
            'projects' => ProjectsRepository::getAllProjects(UsersRepository::getCurrentUser()),
        ], 200);
    }

    /**
     * Creates new project for current user.
     * @param array $details
     * @return Response
     */
    public static function create(array $details) {
        return new Response([
            'status' => 'success',
            'project' => ProjectsRepository::create(UsersRepository::getCurrentUser(), [
                'name' => $details['name'],
                'website' => $details['website'],
                'active' => 1,
            ]),
        ], 200);
    }

    /**
     * Deletes project with given id.
     * @param array $details
     * @return Response
     */
    public static function delete(Project $project) {
        if(ProjectsRepository::getAllProjects(UsersRepository::getCurrentUser())->count() == 1) {
            return new Response([
                'status' => 'error',
                'message' => 'You cannot delete the default project!',
            ], 400);
        }

        ProjectsRepository::deleteProject($project);

        if(!UsersRepository::getCurrentUser()->defaultProject) {
            UsersRepository::setDefaultProject(UsersRepository::getCurrentUser(), ProjectsRepository::getAllProjects(UsersRepository::getCurrentUser())->first());
        }

        return new Response([
            'status' => 'success',
            'message' => 'Project has been deleted',
        ], 200);
    }

    /**
     * Returns the project details.
     * @param Project $project
     * @return Project
     */
    public static function details(Project $project) {
        return $project->makeHidden(['user', 'id', 'user_id']);
    }

    /**
     * Updates the project with the provided details.
     * @param Project $project
     * @param array $details
     * @return Response
     */
    public static function updateProject(Project $project, array $details) {
        if(isset($details['name'])) ProjectsRepository::updateAttribute($project, 'name', $details['name']);
        if(isset($details['website'])) ProjectsRepository::updateAttribute($project, 'website', $details['website']);
        if(isset($details['active'])) ProjectsRepository::updateAttribute($project, 'active', $details['active'] == 1 ? 1 : 0);
        return new Response([
            'status' => 'success',
            'project' => self::details($project),
        ]);
    }
}