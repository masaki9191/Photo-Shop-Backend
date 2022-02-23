<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $times = config('myconfig.times');
        $stations = config('myconfig.stations');
        return [
            'id' => $this->id,
            'description' => $this->description,
            'time' => $times[$this->time-1],
            'time_id' => $this->time,
            'thumbnail' => $this->thumbnail,
            'photos' => $this->images,
            'station' => $stations[$this->station-1],
            'station_id' => $this->station,
            'owner' => $this->owner->name,
            'owner_id' => $this->owner->id,
            'created_at' => $this->created_at->format('m/d/Y'),
            'updated_at' => $this->updated_at->format('m/d/Y'),
        ];
    }
}