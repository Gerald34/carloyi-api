<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Hash;

class GeneratePasswordTokenResource extends Resource
{
    public static function generatePasswordToken($email) {
        return  Hash::make($email);
    }
}
