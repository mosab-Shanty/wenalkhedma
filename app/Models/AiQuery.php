<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AiQuery extends Model
{
    use HasUuids;

    protected $table = 'ai_queries';
    protected $primaryKey = 'query_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'query_text',
        'detected_intent',
        'result_service_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'result_service_id', 'service_id');
    }
}
