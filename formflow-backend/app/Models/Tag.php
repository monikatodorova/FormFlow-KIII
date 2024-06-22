<?php

namespace App\Models;

use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;
    use HasHashId;
    use HasHashIdRouting;

    // Config
    public $fillable = ['name', 'user_id'];
    protected $hidden = ['id', 'user_id', 'created_at', 'updated_at'];
    protected $appends = ['hashId'];
    protected $table = 'tags';

    // Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function submissions() {
        return $this->belongsToMany(Submission::class, 'submissions_tags');
    }

    // Methods
    public function checkOwnership($user) {
        return $this->user->id == $user->id;
    }
}
