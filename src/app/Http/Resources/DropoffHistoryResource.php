<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class DropoffHistoryResource extends JsonResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        self::withoutWrapping();
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cacheKey = 'image_url_' . $this->id;
        $imageUrl = Cache::get($cacheKey);

        $disk = config('app.env') === 'local' ? 's3_local_read' : 's3';

        // キャッシュがない場合は新しい署名付きURLを生成
        if (!$imageUrl) {
            $imageUrl = Storage::disk($disk)->temporaryUrl(
                $this->image_path,
                now()->addMinutes(1)
            );

            // URLをキャッシュに保存（1分間）
            Cache::put($cacheKey, $imageUrl, now()->addMinutes(1));
        }

        $customerWithTrashed = $this->customer()->withTrashed()->first();
        $townWithTrashed = $customerWithTrashed->town()->withTrashed()->first();

        return [
            'first_name' => $customerWithTrashed->first_name,
            'last_name' => $customerWithTrashed->last_name,
            'address' => $townWithTrashed->name . $customerWithTrashed->address_number . ' ' . $customerWithTrashed->room_number,
            'image_url' => $imageUrl,
            'created_at' => (new \DateTime($this->created_at))->format('Y年m月d日 H時i分'),
        ];
    }
}
