<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $table = 'm_propinsi';

    protected $keyType = 'string';
    public $incrementing = false;

    public function cities(){
        return $this->hasMany(City::class, 'id_propinsi', 'id');
    }

    public function districts(){
        return $this->hasMany(District::class, 'id_propinsi', 'id');
    }
}
