<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    protected $table = 'menus';

    use HasFactory, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'description',
        'image_url',
        'portion',
        'price',
        'him_rating',
        'her_rating',
        'overall_rating',
        'is_fav',
        'place_id',
    ];

    protected $casts = [
        'is_fav' => 'boolean',
        'him_rating' => 'float',
        'her_rating' => 'float',
        'overall_rating' => 'float',
        'price' => 'integer',  
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
