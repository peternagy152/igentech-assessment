<?php

namespace App\Http\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class OtpService
{

    public function generateOtp(): array
    {
        return [
            'value' => rand(100000, 999999),
            'expires_at' => Carbon::now()->addMinutes(5),
        ];
    }

    public function validateOTP($phone, $otp)
    {

        $cache_record  = cache::get($phone);

        if ($cache_record == NULL) return false;
        if ($cache_record['otp'] != $otp) return false;

        cache::put($phone, ["otp" => $otp, "verified" => 1], 3600);
        return true;
    }


    //Simulation for Third Party API for sending sms carrying the otp - Example for providers : VictoryLink
    public function sendSMS($phone, $otp)
    {
        return [
            "status" => "success",
            "msg" => "SMS send with OTP"
        ];
    }

}
