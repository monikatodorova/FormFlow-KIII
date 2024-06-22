<?php

namespace App\Models;

use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;
    use HasHashId;
    use HasHashIdRouting;

    // Config
    protected $fillable = ['email', 'form_id', 'verified'];
    protected $hidden = ['id', 'created_at', 'updated_at',];
    protected $appends = ['hashId'];

    // Relations
    public function forms() {
        return $this->belongsTo(Form::class);
    }

    // Methods
    public function getEmail() {
        return $this->email;
    }

}