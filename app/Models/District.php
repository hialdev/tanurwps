<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $table = 'm_kecamatan';
    protected $keyType = 'string';
    public $incrementing = false;

    public function province()
    {
        return $this->belongsTo(Province::class, 'id_propinsi', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'id_kota', 'id');
    }
}
