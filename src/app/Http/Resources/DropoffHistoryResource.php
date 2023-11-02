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
        // $cacheKey = 'image_url_' . $this->id;

        // $imageUrl = Cache::get($cacheKey);

        // // キャッシュがない場合は新しいURLを生成
        // if (!$imageUrl) {
        //     $imageUrl = Storage::disk('s3')->temporaryUrl(
        //         $this->image_path,
        //         now()->addMinutes(1)
        //     );

        //     // URLをキャッシュに保存（1分間）
        //     Cache::put($cacheKey, $imageUrl, now()->addMinutes(1));
        // }

        $imageUrl = '/storage/sample.jpg';

        return [
            'first_name' => $this->customer->first_name,
            'last_name' => $this->customer->last_name,
            'address' => $this->customer->town->name . $this->customer->address_number . ' ' . $this->customer->room_number,
            'image_url' => $imageUrl,
            'created_at' => (new \DateTime($this->created_at))->format('Y年m月d日 H時i分'),
        ];
    }
}
