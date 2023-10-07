<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Technician extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'phone_number' => $this->phone_number,
            'active_unit_id' => $this->active_unit_id,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
