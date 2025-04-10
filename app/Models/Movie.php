<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    const DELETED_AT = 'deletedAt';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'image_url',
        'genre',
        'origin',
        'him_rating',
        'her_rating',
    ];
}
