<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspacePilgrim extends Model
{
    use HasFactory;
    protected $table = 'workspace_pilgrims';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id', 'workspace_id', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
