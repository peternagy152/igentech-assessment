<?php

use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpsController;
use App\Http\Controllers\UsersController;



Route::prefix('register')->group(function(){

    Route::post('/' , [UsersController::class , "register"]) ; 
    Route::post('/otp/send' , [OtpsController::class , "sendPhoneOTP"]) ; 
    Route::post('/otp/verify' , [OtpsController::class , "verifyPhoneOtp"]) ; 

});

Route::prefix('login')->group(function(){

    Route::post('/' , [UsersController::class , "login"]);
    Route::post('/otp/send' , [OtpsController::class , 'SendLoginOTP']) ;

});



Route::get('/user', [UsersController::class , 'showProfile'])->middleware('auth:sanctum');
Route::post('/user', [UsersController::class , 'updateProfile'])->middleware('auth:sanctum');
Route::delete('/user', [UsersController::class , 'deleteProfile'])->middleware('auth:sanctum');
Route::get('/user/devices', [DeviceController::class , 'listDevices'])->middleware('auth:sanctum');






