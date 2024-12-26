<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceController extends Controller
{
    

    public function listDevices(Request $request){

        return $request->user()->devices  ; 
    
    }
}
