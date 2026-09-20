<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasUuids;

    protected $primaryKey = 'service_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'phone',
        'current_status',
        'opening_hours',
        'is_verified',
        'approval_status',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_verified' => 'boolean',
    ];


    public function owner()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            ServiceCategory::class,
            'category_id',
            'category_id'
        );
    }

    public function location()
    {
        return $this->hasOne(
            Location::class,
            'service_id',
            'service_id'
        );
    }

    public function reports()
    {
        return $this->hasMany(
            Report::class,
            'service_id',
            'service_id'
        );
    }

    public function statusUpdates()
    {
        return $this->hasMany(
            ServiceStatusUpdate::class,
            'service_id',
            'service_id'
        );
    }

    public function documents()
    {
        return $this->hasMany(
            Document::class,
            'service_id',
            'service_id'
        );
    }

    public function favorites()
    {
        return $this->hasMany(
            Favorite::class,
            'service_id',
            'service_id'
        );
    }
}
