<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DropoffHistoryListResource extends JsonResource
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
        return [
            'id' => $this->id,
            'customerId' => $this->customer->id,
            'firstName' => $this->customer->first_name,
            'lastName' => $this->customer->last_name,
            'address' => $this->customer->town->name . $this->customer->address_number . ' ' . $this->customer->room_number,
        ];
    }
}
