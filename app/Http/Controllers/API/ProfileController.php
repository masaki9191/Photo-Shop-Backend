<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Resources\User as UserResource;
use Validator;
use App\Models\User;

class ProfileController extends BaseController
{
    use MediaUploadingTrait;

    public function update(Request $request) {

        $message = [
            'name.required' => 'ユーザー名: 未入力項目があります。',
            'avatar.required' => 'アバター: 未入力項目があります。'
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $user = User::find(auth()->user()->id);
        $user->name = $request->name;        
        $user->save();

        $path = storage_path('tmp/uploads');
        if($request->has('avatar'))
        {
            $media = $user->avatar();

            if ($media != null ) {
                $media->delete();
            }

            $file = $request->file('avatar');
            $name = uniqid() . '_' . trim($file->getClientOriginalName());
            $file->move($path, $name);
            $user->addMedia(storage_path('tmp/uploads/' . $name))->toMediaCollection('avatar');
        }        
        $newUser = User::find(auth()->user()->id);
        return $this->sendResponse(new UserResource($newUser), '変更しました。');
    }

    public function show() {
        $user = auth()->user();
        return $this->sendResponse(new UserResource($user), 'Profile show.');
    }
}
