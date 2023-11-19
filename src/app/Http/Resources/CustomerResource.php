<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
        $customerData = [
            'id' => $this->id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'town_id' => $this->town->id,
            'town_name' => $this->town->name,
            'address_number' => $this->address_number,
            'building_name' => $this->building_name,
            'company' => $this->company,
            'room_number' => $this->room_number,
            'dropoffs' => DropoffResource::collection($this->dropoffs),
            'description' => $this->description
        ];

        return $customerData;
    }
}
