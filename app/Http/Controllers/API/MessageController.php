<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Message;
use App\Http\Resources\Message as MessageResource;
use App\Http\Resources\Room as RoomResource;
use App\Http\Resources\Notification as NotificationResource;
use Carbon\Carbon;

class MessageController extends BaseController
{
    public function index(Request $request) {
        $user = auth()->user();
        $user_id = $user->id;
        $rooms1 = Room::whereHas('product.owner', function ($query) use($user_id) {
                return $query->where('users.id', $user_id);
            })
            ->has('messages')
            ->get();
        $rooms2 = Room::where('user_id', $user_id)
            ->has('messages')->get();
        $rooms = $rooms1->concat($rooms2);
        return $this->sendResponse(RoomResource::collection($rooms), 'Rooms fetched.');  
    }

    public function store(Request $request) {
        $user_id = auth()->user()->id;   
        $data = [
            'room_id'=> $request->room_id,
            'user_id'=> $user_id,
            'text' => $request->text
        ];
        Message::create($data);
        return $this->sendResponse([], 'Message Added.');
    }

    public function show(Request $request) {
        $date = Carbon::now();
        $room_id = $request->room_id;
        Message::where('room_id', $room_id)->where('user_id', '!=', auth()->user()->id)->update(['updated_at' => $date->toDateTimeString()]);
        $messages = Message::where('room_id', $room_id)->get();
        $data = [];
        $data["messages"] = MessageResource::collection($messages);
        $room  = Room::where("id", $room_id)->first();
        $product = $room->product;
        $data["product"] = $product;
        $data["product"]["thumbnail"] = $product->thumbnail;
        return $this->sendResponse($data , 'Messages fetched.');
    }

    public function room(Request $request) {
        $product_id = $request->product_id;
        $user_id = auth()->user()->id;        
        $data = [
            'product_id'=> $product_id,
            'user_id'=> $user_id
        ];
        $room =Room::firstOrCreate($data);
        $messages = Message::where( 'room_id', $room->id )->get();
        return $this->sendResponse($room, 'Messages fetched.');
    }

    public function notification(Request $request) {
        $user = auth()->user();
        $user_id = $user->id;
        $rooms1 = Room::whereHas('product.owner', function ($query) use($user_id) {
                return $query->where('users.id', $user_id);
            })
            ->whereHas('messages.user', function ($query) use($user_id) {
                return $query->where('users.id', '!=' ,$user_id)->whereColumn('messages.created_at','messages.updated_at');
            })
            ->get();
        $rooms2 = Room::where('user_id', $user_id)
            ->whereHas('messages.user', function ($query) use($user_id) {
                return $query->where('users.id', '!=' ,$user_id)->whereColumn('messages.created_at','messages.updated_at');
            })
            ->get();
        $rooms = $rooms1->concat($rooms2);
        return $this->sendResponse(NotificationResource::collection($rooms), 'Rooms fetched.');  
    }
}
