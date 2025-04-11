<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as AuthUser;

class User extends AuthUser implements FilamentUser
{
    use SoftDeletes, HasFactory;

    protected $fillable = ["name", "email", "password"];

    protected $hidden = ["password"];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
