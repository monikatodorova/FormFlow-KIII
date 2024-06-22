<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersRepository {

    /**
     * Tries to authenticate the user with the provided credentials.
     * @param $email
     * @param $password
     * @return bool
     */
    public static function login($email, $password) {
        return Auth::attempt(['email' => $email, 'password' => $password]);
    }

    /**
     * Generates OAuth 2.0 token for the provided user.
     * @param $user
     * @return mixed
     */
    public static function generateAccessToken($user) {
        return $user->createToken("appToken")->accessToken;
    }

    /**
     * Returns the current authenticated user.
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function getCurrentUser() {
        return Auth::user();
    }

    /**
     * Creates new user with the given details
     * @param array $details
     * @return User
     */
    public static function createUser(array $details) {
        return User::create([
            'name' => $details['name'],
            'email' => $details['email'],
            'password' => Hash::make($details['password']),
            'package_id' => 1
        ]);
    }

    /**
     * Sets the given project as default for the given user
     * @param User $user
     * @param Project $project
     * @return void
     */
    public static function setDefaultProject($user, $project) {
        $user->setAttribute('default_project_id', $project->id);
        $user->save();
    }
}
