<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory, SoftDeletes;

    const DELETED_AT = 'deletedAt';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'image_url',
        'parking',
        'wifi',
        'room',
        'open_hour',
        'close_hour',
        'price_min',
        'price_max',
        'him_rating',
        'her_rating',
        'overall_rating',
        'is_fav',
        'map_url',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
