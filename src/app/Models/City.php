<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, SoftDeletes, MassPrunable;

    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'prefecture_id'
    ];

    public function prunable(): Builder
    {
        return static::onlyTrashed()->whereDoesntHave('towns', function ($query) {
            $query->withTrashed();
        });
    }

    public function prefecture(): BelongsTo
    {
        return $this->belongsTo(Prefecture::class);
    }
    public function towns(): HasMany
    {
        return $this->hasMany(Town::class);
    }
}
