<?php

namespace App\Models\Subscriptions;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    // Config
    public $timestamps = false;

    // Relations
    public function users() {
        return $this->hasMany(User::class);
    }

    public function getAvailableProjects() {
        return $this->projects;
    }

    public function getAvailableSubmissions() {
        return $this->submissions;
    }

    public function getArchiveDays() {
        return $this->archive;
    }

}
