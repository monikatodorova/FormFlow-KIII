<?php

namespace App\Http\Controllers;

use App\Services\SubmissionsService;
use App\Models\Tag;
use App\Models\Submission;
use App\Repositories\UsersRepository;
use Illuminate\Http\Request;

class SubmissionsController extends Controller {

    public function index(Request $request) {
        return SubmissionsService::getAllSubmissionsForProject(UsersRepository::getCurrentUser()->defaultProject, $request->get('status'));
    }

    public function store($formSecret, Request $request) {
        return SubmissionsService::saveSubmission($formSecret, $request->all());
    }

    public function details(Submission $submission) {
        return SubmissionsService::details($submission);
    }

    public function addTag(Submission $submission, Request $request) {
        return SubmissionsService::addTag($submission, Tag::keyFromHashId($request->post("id")));
    }

    public function deleteTag(Submission $submission, Tag $tag) {
        return SubmissionsService::removeTag($submission, $tag);
    }

}