<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Notification extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lasted_comment = $this->messages->last();
        $lasted_comment_user_name =  $lasted_comment->user->name;
        $lasted_comment_user_avatar =  $lasted_comment->user->avatar_url();
        $lasted_comment_text = $lasted_comment->text;
        $lasted_comment_at = $lasted_comment->created_at;
        return [
            'id' => $this->id,
            'lasted_comment_user_name' => $lasted_comment_user_name,
            'lasted_comment_user_avatar' => $lasted_comment_user_avatar,
            'lasted_comment_text' => $lasted_comment_text,  
            'lasted_comment_at' =>  $lasted_comment_at,   
        ];  
    }
}
