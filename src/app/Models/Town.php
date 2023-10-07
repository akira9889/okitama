<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'city_id'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function userTowns()
    {
        return $this->hasMany(UserTown::class);
    }
}
