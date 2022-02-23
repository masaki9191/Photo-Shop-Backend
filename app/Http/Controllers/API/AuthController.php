<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Http\Resources\User as UserResource;

class AuthController extends BaseController
{
    public function signin(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $authUser = Auth::user(); 
            $success['token'] =  $authUser->createToken('LaravelAuthApp')->accessToken;
            $success['email_verified_at'] = $authUser->email_verified_at;
            $success['user'] =  new UserResource($authUser);
            return $this->sendResponse($success, 'User signed in');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' =>  'required|email|unique:users',
            'password' => 'required',
            'confirmPassword' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        //event(new Registered($user));
        $success['token'] =  $user->createToken('LaravelAuthApp')->accessToken;
        $success['user'] =  new UserResource($user);
   
        return $this->sendResponse($success, 'User created successfully.');
    }

    
    // method for user logout and delete token
    public function logout()
    {
        auth()->user()->tokens()->delete();
        $success = [];
        return $this->sendResponse($success, 'User signed in');
    }
}
