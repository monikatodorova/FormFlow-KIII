<?php

namespace App\Services;

use App\Repositories\UsersRepository;
use App\Repositories\ProjectsRepository;
use Illuminate\Http\Response;

class UsersService {

    /**
     * Tries to authenticate the user with the provided email and password.
     * @param $email
     * @param $password
     * @return Response
     */
    public static function authenticate($email, $password) {
        if(!UsersRepository::login($email, $password)) {
            return new Response([
                'status' => 'error',
                'message' => "Invalid email or password.",
            ], 401);
        }
        return new Response([
            'status' => 'success',
            'token' => UsersRepository::generateAccessToken(UsersRepository::getCurrentUser()),
        ], 200);
    }

    /**
     * Tries to register new user with the given data.
     * @param array $details
     * @return Response
     */
    public static function register(array $details) {
        $user = UsersRepository::createUser([
            'name' => $details['name'],
            'email' => $details['email'],
            'password' => $details['password'],
            'package_id' => 1,
        ]);

        // Create Default Project
        $project = ProjectsRepository::create($user, [
            'name' => $details['project']['name'] ?? 'My Project',
            'website' => $details['project']['website'] ?? null,
            'active' => 1,
        ]);
        UsersRepository::setDefaultProject($user, $project);

        // Create Demo Form
        $testForm = FormsService::createForm($project, [
            'name' => 'Test Form (demo)',
            'recipients' => [
                [
                    'id' => -1,
                    'email' => $details['email'],
                ],
            ],
            'color_id' => 32,
        ], true);

        // Create Default Form
        FormsService::createForm($project, [
            'name' => 'Contact Form',
            'recipients' => [
                [
                    'id' => -1,
                    'email' => $details['email'],
                ],
            ],
            'color_id' => 26,
        ]);

        // Create demo submissions
        SubmissionsService::generateDemoSubmissions($testForm);

        // Create starter tags
        TagsService::createDemoTags($user);

        return new Response([
            'status' => "success",
            'user' => $user->makeHidden(['created_at', 'updated_at', 'id']),
            'project' => $project,
            'token' => UsersRepository::generateAccessToken($user),
        ], 200);
    }

    public static function details() {
        $user = UsersRepository::getCurrentUser();
        if ($user->defaultProject !== null) {
            $user->defaultProject->makeHidden(['id']);
        }
        return new Response([
            'status' => 'success',
            'user' => $user->makeHidden(['created_at', 'updated_at', 'id'])
        ], 200);
    }

    public static function setDefaultProject($projectId) {
        $user = UsersRepository::getCurrentUser();
        $project = ProjectsRepository::getById($projectId);
        UsersRepository::setDefaultProject($user, $project);
        return new Response([
            'status' => 'success',
            'project' => $project,
        ], 200);
    }
}