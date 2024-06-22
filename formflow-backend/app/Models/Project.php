<?php

namespace App\Models;

use Deligoez\LaravelModelHashId\Traits\HasHashId;
use Deligoez\LaravelModelHashId\Traits\HasHashIdRouting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    use HasHashId;
    use HasHashIdRouting;

    // Config
    protected $fillable = ['name', 'website', 'user_id'];
    protected $hidden = ['id', 'active', 'created_at', 'updated_at',];
    protected $appends = ['hashId'];
    public const PROJECT_CATEGORIES = [
        'Automotive',
        'Business Support & Supplies',
        'Computers & Electronics',
        'Construction & Contractors',
        'Education',
        'Entertainment',
        'Food & Dining',
        'Health & Medicine',
        'Home & Garden',
        'Legal & Financial',
        'Manufacturing, Wholesale, Distribution',
        'Merchants',
        'Miscellaneous',
        'Personal Care & Services',
        'Real Estate',
        'Travel & Transportation',
        'Other'
    ];

    // Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function forms() {
        return $this->hasMany(Form::class);
    }

    // Methods
    public function getId() {
        return $this->id;
    }

    public function checkOwnership($user) {
        return $this->user->id == $user->id;
    }

    public function getRouteKeyName()
    {
        return 'hashId';
    }

}