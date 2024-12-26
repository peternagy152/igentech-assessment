<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

    /**
     * Register Endpoint
     *
     * @param RegisterRequest $request
     * @return void
     */
    public function register(RegisterRequest $request)
    {
        //Check Verification 
        $cache_record = cache::get($request->phone);


        if ($cache_record == NULL || $cache_record['verified'] != 1) {
            return response()->json([
                'status' => 'error',
                "msg" => 'Phone must be verified'
            ], 400);
        }

        // Create a User Entry 
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "email_hashed" => Hash("SHA256", $request->email),
            "phone_hashed" => Hash("SHA256", $request->phone)
        ]);

        //Store User Profile image 
        $user->addMediaFromRequest('profile_img')->toMediaCollection('users-images');

        //Log User Devices 
        if (!Device::where("fcm_token", $request->fcm_token)->first()) {
            Device::create([
                "user_id" => $user->id,
                "device_id" => $request->device_id,
                "device_type" => $request->device_type,
                "fcm_token" => $request->fcm_token
            ]);
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "status" => "success",
            "msg" => "Account Created",
            "token" => $token
        ], 201);
    }


    /**
     * Login function with phone only and set token for the registered user
     *
     * @param LoginRequest $request
     * @return void
     */

    public function login(LoginRequest $request)
    {

        $hashed_phone = Hash("SHA256", $request->phone);

        //Verify OTP 
        $cached_record = cache::get($request->phone);
        if ($cached_record == NULL || $cached_record['otp'] != $request->otp) {
            return response()->json([
                "status" => "error",
                "msg" => "Invalid or expired OTP.",
            ], 401);
        }
        cache::forget($request->phone);


        $user  = User::where('phone_hashed', $hashed_phone)->first();
        //Log User Devices 

        if (!Device::where("fcm_token", $request->fcm_token)->first()) {
            Device::create([
                "user_id" => $user->id,
                "device_id" => $request->device_id,
                "device_type" => $request->device_type,
                "fcm_token" => $request->fcm_token
            ]);
        }
        //create user token 
        $token = $user->createToken('auth_token')->plainTextToken;
        Auth::login($user);

        return response()->json([
            "status" => "success",
            "msg" => "Logged in successfully",
            "user" => $user,
            "token" => $token
        ], 201);
    }


    /**
     * Endpoint for retrieving user profile info
     *
     * @param Request $request
     * @return void
     */
    public function showProfile(Request $request)
    {
        $profile_img = $request->user()->getMedia("users-images");
        return response()->json( [
            "status" => "success" , 
            "msg" => "Account data retrieved" , 
            "user" => [
                "id" => $request->user()->id,
                "name" => $request->user()->name,
                "email" => $request->user()->email,
                "phone" => $request->user()->phone,
                "profile_img" => $profile_img[0]->original_url,
            ]
        ] , 200) ; 
    }




    /**
     * Endpoint for updating user profile data
     * Phone cannot be changed as it acts like the unique identifier for the account
     *
     * @param UpdateProfileRequest $request
     * @return void
     */
    public function updateProfile(UpdateProfileRequest $request){
        $user = $request->user() ; 

        $user->name = $request->name ; 
        $user->email = $request->email ; 

        $user->clearMediaCollection('users-images');
        $user->addMediaFromRequest('profile_img')->toMediaCollection('users-images');
        $user->save() ; 
        return response()->json([
            'status' => "success" , 
            "msg" => "Profile data updated" , 
            "user" => [
                "id" => $user->id , 
                "name" => $user->name , 
                "email" => $user->email , 
                "phone" => $user->phone , 
                "profile_img" => $user->getMedia('users-images')[0]->original_url,

            ]
        ] , 200);

    }

    /**
     * Endpoint for removing user account with all subsequent data 
     *
     * @param Request $request
     * @return void
     */
    public function deleteProfile(Request $request){
        $user = $request->user() ; 
        
        //Images
        $user->clearMediaCollection('users-images');
        //Devices
        $user->devices()->delete() ; 
        //Tokens
        $user->tokens()->delete() ; 
        // Remove User itself
        $user->delete() ; 

        return response()->json( [
            'status' => "success" , 
            "msg" => "Account removed successfully" , 
        ] , 200) ; 

    }
}
