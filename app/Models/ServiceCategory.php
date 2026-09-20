<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasUuids;

    protected $primaryKey = 'category_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['name', 'description', 'status'];

    public function services()
    {
        return $this->hasMany(
            Service::class,
            'category_id',
            'category_id'
        );
    }
}
