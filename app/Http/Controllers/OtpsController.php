<?php

namespace App\Http\Controllers;

use App\Http\Requests\OtpRequest;
use App\Http\Requests\PhoneRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Services\OtpService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class OtpsController extends Controller
{
    public $otpService ; 
    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }



    /**
     * Send OTP to Phone For Register Endpoint  
     *
     * @param OtpRequest $request
     * @return void
     */
    
    public function sendPhoneOTP(OtpRequest $request)
    {

        $new_otp = $this->otpService->generateOTP();

        // Cached for 10 mins - During Register Process
        Cache::put($request->phone, ["otp" => $new_otp['value'], "verified" => 0], 600);


        //Send SMS to user 
        $sms_response = $this->otpService->sendSMS($request->phone, $new_otp['value']);

        if ($sms_response['status'] == 'success') {
            return response()->json([
                "status" => "success",
                "msg" => "An SMS Sent to you with OTP",
                "otp" => (string)$new_otp['value']
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "msg" => "Something went wrong , Please try again later"
            ], 400);
        }


    }


    /**
     * Verify the provided phone OTP Endpoint.
     *
     * @param VerifyOtpRequest $request
     * @return void
     */
    
    public function verifyPhoneOtp(VerifyOtpRequest $request)
    {

        if ($this->otpService->validateOTP($request->phone, $request->otp)) {
            return response()->json([
                "status" => "success",
                "msg" => "Phone validated successfully."
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "msg" => "Invalid or expired OTP."
            ], 400);
        }
    }



    /**
     * Send the otp for the login 
     *
     * @param PhoneRequest $request
     * @return void
     */

    public function sendLoginOTP(PhoneRequest $request)
    {
      
        $new_otp = $this->otpService->generateOtp();
        Cache::put($request->phone, ['otp' => $new_otp['value'] , "verified" => 0 ] ,  300);

        $sms_response = $this->otpService->sendSMS($request->phone, $new_otp['value']);

        if ($sms_response['status'] == 'success') {
            return response()->json([
                "status" => "success",
                "msg" => "An SMS Sent to you with OTP",
                "otp" => (string)$new_otp['value']
            ], 200);
        } else {
            return response()->json([
                "status" => "error",
                "msg" => "Something went wrong , Please try again later"
            ], 400);
        }


    }

}
