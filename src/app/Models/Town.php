<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Town extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'city_id'
    ];

    public function prunable(): Builder
    {
        return static::onlyTrashed()->whereDoesntHave('customers', function ($query) {
            $query->withTrashed();
        });
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
