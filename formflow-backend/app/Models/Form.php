<?php

namespace App\Models;

use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    use HasHashId;
    use HasHashIdRouting;

    // Config
    protected $with = ['color'];
    protected $withCount = ['submissions AS total_submissions', 'newSubmissions as new_submissions'];
    protected $hidden = ['id', 'project_id', 'color_id', 'created_at', 'updated_at'];
    protected $fillable = ['name', 'color_id', 'project_id'];
    protected $appends = ['hashId'];

    // Relations
    public function user() {
        return $this->project->user;
    }

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function recipients() {
        return $this->hasMany(Recipient::class);
    }

    public function submissions() {
        return $this->hasMany(Submission::class);
    }

    public function newSubmissions() {
        return $this->hasMany(Submission::class)->where('status', '=', 'new');
    }

    public function color() {
        return $this->belongsTo(Color::class);
    }

    // Methods
    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function checkOwnership($user) {
        return $this->user()->id == $user->id;
    }

    public function getSecretAttribute() {
        return $this->getHashIdAttribute();
    }

}