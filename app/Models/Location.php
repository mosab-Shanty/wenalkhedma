<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasUuids;

    protected $primaryKey = 'location_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'service_id', 'governorate', 'city',
        'area', 'address_text', 'latitude', 'longitude', 'geo_point'
    ];

    public function service()
    {
        return $this->belongsTo(
            Service::class,
            'service_id',
            'service_id'
        );
    }
}
