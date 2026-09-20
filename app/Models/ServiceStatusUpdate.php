<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ServiceStatusUpdate extends Model
{
    use HasUuids;

    protected $primaryKey = 'update_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['service_id', 'old_status', 'new_status', 'updated_by'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
