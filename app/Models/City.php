<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'm_kota';
    protected $connection = 'wps';
    protected $keyType = 'string';
    public $incrementing = false;

    public function province()
    {
        return $this->belongsTo(Province::class, 'id_propinsi', 'id');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'id_kota', 'id');
    }
}
