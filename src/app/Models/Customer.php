<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'town_id', 'address_number',
        'room_number', 'is_dropoff_possible', 'description'
    ];

    public function dropoffs(): BelongsToMany
    {
        return $this->belongsToMany(Dropoff::class);
    }
}
