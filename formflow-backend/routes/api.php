<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\SubmissionsController;
use App\Http\Controllers\TagsController;
use App\Models\Color;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * =========================
 * Login & Registration Routes
 * =========================
 */
Route::post('/login', [UsersController::class, 'login'])
    ->name('api.login')
    ->middleware('cors');

Route::post('/register', [UsersController::class, 'register'])
    ->name('api.register');

Route::get("/colors", function () {
    return Color::query()->orderBy('color')->get()->makeVisible(['id']);
});

/**
 * =========================
 * Save Submissions Route
 * =========================
 */
Route::post("/f/{formSecret}", [SubmissionsController::class, 'store'])
    ->name('api.submissions.store')
    ->middleware('cors');



// Authenticated users
Route::middleware('auth:api')->group(function() {

    /**
     * =========================
     * User Routes
     * =========================
     */
    Route::get('/me', [UsersController::class, 'details'])
        ->name('api.me');
    Route::post('/default-project', [UsersController::class, 'updateDefaultProject'])
        ->name('api.default.project');


    /**
     * =========================
     * Project Routes
     * =========================
     */
    Route::get('/projects', [ProjectsController::class, 'index'])
        ->name('api.projects.index');
    Route::post('/projects', [ProjectsController::class, 'create'])
        ->name('api.projects.create');
    Route::delete("/projects/{project}", [ProjectsController::class, 'delete'])
        ->name('api.projects.delete')
        ->middleware('ownership.project');
    Route::get("/projects/{project}", [ProjectsController::class, 'details'])
        ->name('api.projects.details')
        ->middleware('ownership.project');
    Route::put("/projects/{project}", [ProjectsController::class, 'update'])
        ->name('api.projects.update')
        ->middleware('ownership.project');
    Route::get("/projects/{project}/statistics", [ProjectsController::class, 'statistics'])
        ->name('api.projects.statistics')
        ->middleware('ownership.project');
    
        
    /**
     * =========================
     * Form Routes
     * =========================
     */
    Route::get('/projects/{project}/forms', [FormsController::class, 'index'])
        ->name('api.forms.index')
        ->middleware('ownership.project');

    Route::post("/projects/{project}/forms", [FormsController::class, 'store'])
        ->name('api.forms.store')
        ->middleware('ownership.project');

    Route::get("/projects/{project}/forms/{form}", [FormsController::class, 'details'])
        ->name('api.forms.details')
        ->middleware('ownership.project')
        ->middleware('ownership.form')
        ->middleware('relationship.project-form');

    Route::put("/projects/{project}/forms/{form}", [FormsController::class, 'update'])
        ->name('api.forms.update')
        ->middleware('ownership.project')
        ->middleware('ownership.form')
        ->middleware('relationship.project-form');

    Route::delete("/projects/{project}/forms/{form}", [FormsController::class, 'delete'])
        ->name('api.forms.delete')
        ->middleware('ownership.project')
        ->middleware('ownership.form')
        ->middleware('relationship.project-form');
    
    Route::get("/projects/{project}/forms/{form}/submissions", [FormsController::class, 'submissions'])
        ->name('api.forms.submissions')
        ->middleware('ownership.project')
        ->middleware('ownership.form')
        ->middleware('relationship.project-form');

    Route::get("/projects/{project}/forms/{form}/statistics", [FormsController::class, 'statistics'])
        ->name('api.forms.statistics')
        ->middleware('ownership.project')
        ->middleware('ownership.form')
        ->middleware('relationship.project-form');


    /**
     * =========================
     * Submission Routes
     * =========================
     */
    Route::get('/submissions', [SubmissionsController::class, 'index'])
        ->name('api.submissions.index');
    
    Route::get("/submissions/{submission}", [SubmissionsController::class, 'details'])
        ->name('api.submissions.details')
        ->middleware('ownership.submission');
    
    Route::post("/submissions/{submission}/tags", [SubmissionsController::class, 'addTag'])
        ->name('api.submissions.tags.add')
        ->middleware('ownership.submission');

    Route::delete("/submissions/{submission}/tags/{tag}", [SubmissionsController::class, 'deleteTag'])
        ->name('api.submissions.tags.delete')
        ->middleware('ownership.submission');


    /**
     * =========================
     * Tags Routes
     * =========================
     */
    Route::get('/tags', [TagsController::class, 'index'])
        ->name('api.tags.index');
    
    Route::post('/tags', [TagsController::class, 'store'])
        ->name('api.tags.store');
    
    Route::delete('/tags/{tag}', [TagsController::class, 'delete'])
        ->name('api.tags.delete')
        ->middleware('ownership.tag');

    Route::put('/tags/{tag}', [TagsController::class, 'update'])
        ->name('api.tags.update')
        ->middleware('ownership.tag');

});
