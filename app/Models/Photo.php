<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
    'person_id',
    'path',
    'title',
    'caption',
     'order',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    // Get full URL for the photo
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
