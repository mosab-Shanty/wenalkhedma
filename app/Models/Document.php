<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasUuids;

    protected $primaryKey = 'document_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'service_id',
        'file_type',
        'file_url',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }
}
