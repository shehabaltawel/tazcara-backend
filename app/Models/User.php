<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['first_name', 'last_name', 'email', 'mobile', 'password', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
#[RouteKey('uuid')]

/**
 * User Model
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
