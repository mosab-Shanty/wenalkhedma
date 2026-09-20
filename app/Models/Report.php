<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasUuids;

    protected $primaryKey = 'report_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'service_id', 'reported_status',
        'note', 'image_url', 'report_status'
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'user_id'
        );
    }

    public function service()
    {
        return $this->belongsTo(
            Service::class,
            'service_id',
            'service_id'
        );
    }
}
