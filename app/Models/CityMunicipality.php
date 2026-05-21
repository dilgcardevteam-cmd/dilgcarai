<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityMunicipality extends Model
{
    protected $table = 'cities_municipalities';

    protected $fillable = ['province_id', 'code', 'name', 'type'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
