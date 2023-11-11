<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DropoffHistory extends Model
{
    use HasFactory, Prunable;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'customer_id',
        'image_path'
    ];

    //作成してから1年以上前のレコードを自動削除
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subYear());
    }

    //S3の画像を削除
    protected function pruning(): void
    {
        Storage::disk('s3')->delete($this->image_path);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
