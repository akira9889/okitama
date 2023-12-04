<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    public $timestamps = false;

    protected $fillable = [
        'first_name', 'last_name', 'full_name',
        'first_kana', 'last_kana', 'full_kana',
        'company',
        'town_id', 'address_number', 'building_name', 'room_number',
        'description', 'absence', 'only_amazon'
    ];

    public function prunable(): Builder
    {
        return static::onlyTrashed()->doesntHave('dropoffHistories');
    }

    public function dropoffs(): BelongsToMany
    {
        return $this->belongsToMany(Dropoff::class)->orderByPivot('dropoff_id');
    }

    public function town(): BelongsTo
    {
        return $this->belongsTo(Town::class);
    }

    public function dropoffHistories(): HasMany
    {
        return $this->HasMany(DropoffHistory::class);
    }
}
