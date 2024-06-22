<?php

namespace App\Models;

use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Submission extends Model
{
    use HasFactory;
    use HasHashId;
    use HasHashIdRouting;

    // Constants
    public const FIELDS_LIMIT = 100;
    public const STATUS_OPTIONS = [
        'new',
        'seen',
    ];

    // Config
    protected $fillable = ['name', 'email', 'fields', 'status', 'form_id'];
    protected $hidden = ['id'];
    protected $appends = ['hashId', 'avatar'];

    // Relations
    public function form() {
        return $this->belongsTo(Form::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class, 'submissions_tags');
    }

    // Getters & setters
    public function getFieldsAttribute($value) {
        if($value == null) return [];
        return json_decode(Crypt::decrypt($value), true);
    }

    public function setFieldsAttribute($value) {
        if($value == null || !is_array($value) || count($value) == 0) {
            $this->attributes['fields'] = Crypt::encrypt('{}');
        } else {
            $this->attributes['fields'] = Crypt::encrypt(json_encode($value));
        }
    }

    // Methods
    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getAvatarAttribute() {
        return [
            'credentials' => $this->shortName(),
            'color' => $this->color()->color,
            'text' => $this->color()->text,
        ];
    }

    public function shortName() {
        $name = $this->name;
        $nameItems = explode(" ", $name);
        $returnable = "";
        foreach($nameItems as $nameItem) {
            $returnable .= mb_substr($nameItem, 0, 1);
        }
        $returnable = mb_strtoupper($returnable, 'UTF-8');
        return mb_substr($returnable, 0, 2);
    }

    public function color() {
        $email = $this->email;
        $colors = Color::query()->orderBy('color')->get()->makeHidden(['id', 'name']);
        $hash = 0;
        for($i = 0; $i < strlen($email); $i++) {
            $hash = $hash + ord($email[$i]);
        }
        $index = abs($hash % count($colors));
        return $colors[$index];
    }

    public function checkOwnership($user) {
        return $this->form->project->user->id == $user->id;
    }
}