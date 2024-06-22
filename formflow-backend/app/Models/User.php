<?php

namespace App\Models;

use App\Models\Subscriptions\Package;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Config
    protected $fillable = ['name', 'email', 'password', 'package_id', 'default_project_id'];
    protected $hidden = ['password',];
    protected $appends = ['avatar'];

    // Relations
    public function projects() {
        return $this->hasMany(Project::class);
    }

    public function defaultProject() {
        return $this->belongsTo(Project::class, 'default_project_id');
    }

    public function tags() {
        return $this->hasMany(Tag::class);
    }

    public function package() {
        return $this->belongsTo(Package::class);
    }

    // Methods
    public function getId() {
        return $this->id;
    }

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
}
