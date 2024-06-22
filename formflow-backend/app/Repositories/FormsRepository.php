<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Form;
use App\Models\Color;

class FormsRepository {

    /**
     * @param Project $project
     * @return array
     */
    public static function getAllFormsForProject(Project $project) {
        return $project->forms->makeHidden(['project_id'])->append(['secret']);
    }

    /**
     * Returns all form ids that are related to the provided project.
     * @param Project $project
     * @return array
     */
    public static function getAllFormIdsForProject(Project $project) {
        return $project->forms()->pluck('id')->toArray();
    }

    /**
     * Creates the form with the provided details, and connects it to the provided project.
     * @param Project $project
     * @param Color $color
     * @param array $details
     * @return mixed
     */
    public static function createForm(Project $project, Color $color, array $details) {
        return Form::create([
            'name' => $details['name'],
            'project_id' => $project->getId(),
            'color_id' => $color->getId(),
        ]);
    }

    /**
     * Clears up & returns the details for the provided form.
     * @param Form $form
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     */
    public static function details(Form $form) {
        return Form::query()->with('recipients')->where("id", "=", $form->getId())->first()->makeHidden(['project_id', 'id', 'project'])->makeVisible(['color_id'])->append(['secret']);
    }

     /**
     * Sets the provided value to the attribute on the given form.
     * @param Form $form
     * @param string $attribute
     * @param $value
     * @return void
     */
    public static function updateAttribute(Form $form, string $attribute, $value) {
        $form->setAttribute($attribute, $value);
        $form->save();
    }

    /**
     * Deletes the provided form.
     * @param Form $form
     * @return void
     */
    public static function delete(Form $form) {
        $form->delete();
    }

    /**
     * Return the form associated with the provided secret key.
     * @param $formSecret
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getFormBySecretKey($formSecret) {
        try {
            return Form::query()->where('id', '=', Form::keyFromHashId($formSecret))->first();
        } catch (\Exception $exception) {
            return null;
        }
    }

}
