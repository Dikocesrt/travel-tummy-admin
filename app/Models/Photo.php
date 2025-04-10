<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    const DELETED_AT = 'deletedAt';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'image_url',
        'place_id',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
