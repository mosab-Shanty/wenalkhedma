<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuids;

    protected $primaryKey = 'notification_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    protected $casts = [
        'is_read' => 'boolean',
    ];
}
