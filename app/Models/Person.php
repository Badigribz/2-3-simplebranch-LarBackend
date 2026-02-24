<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'bio',
        'photo_path',
        'birth_date',      
        'birth_place',
        'death_date',
    ];

    public function parent()
    {
        return $this->belongsTo(Person::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Person::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
