<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Message extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this->user;
        $avatar = $user->avatar_url();
        return [
            'id' => $this->id,
            'text' => $this->text,
            'user_id' => $this->user_id,            
            'user_avatar' => $avatar,      
            'user_name' => $user->name,
            'created_at'=> $this->created_at->diffForHumans()    
        ];
    }
}
