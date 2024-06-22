<?php

namespace App\Http\Controllers;

use App\Models\Project;

use App\Services\UsersService;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function login(Request $request) {
        return UsersService::authenticate($request->post("email"), $request->post("password"));
    }

    public function register(Request $request) {
        return UsersService::register($request->all());
    }

    public function details() {
        return UsersService::details();
    }

    public function updateDefaultProject(Request $request) {
        return UsersService::setDefaultProject(Project::keyFromHashId($request->post("projectId")));
    }
}
