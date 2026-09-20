<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, HasApiTokens;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'password_hash',
        'role',
        'status',
    ];

    protected $hidden = [
        'password_hash',
    ];


    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'moderator';
    }


    public function services()
    {
        return $this->hasMany(Service::class, 'user_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'user_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function aiQueries()
    {
        return $this->hasMany(AiQuery::class, 'user_id');
    }
}
