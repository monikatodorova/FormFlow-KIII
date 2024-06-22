<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\User;

class ProjectsRepository {

    /**
     * @param User $user
     * @return mixed
     */
    public static function getAllProjects(User $user) {
        $projects = $user->projects()->withCount('forms')->orderByDesc('id')->get()->makeHidden(['user_id']);
        foreach ($projects as $project) {
            $project->submissions_count = $project->forms->sum(function ($form) {
                return $form->total_submissions;
            });
            unset($project->forms);
        }

        return $projects;
    }

    /**
     * @param $id
     * @return Project|null
     */
    public static function getById($id) {
        return Project::query()->where("id", "=", $id)->first();
    }

    /**
     * @param User $user
     * @param array $details
     * @return Project|null
     */
    public static function create($user, $details) {
        return Project::create([
            'name' => $details['name'],
            'website' => $details['website'],
            'user_id' => $user->getId(),
            'active' => $details['active'],
        ]);
    }

    /**
     * @param Project $project
     * @return void
     */
    public static function deleteProject(Project $project) {
        $project->delete();
    }

    /**
     * Sets the value to the provided attribute for the given project.
     * @param Project $project
     * @param string $attribute
     * @param $value
     * @return void
     */
    public static function updateAttribute(Project $project, string $attribute, $value)
    {
        $project->setAttribute($attribute, $value);
        $project->save();
    }

}
