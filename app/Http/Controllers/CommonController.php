<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Helper;

class CommonController extends Controller
{
    public function generatePassword() {
        $password = Helper::generatePassword();
        return response()->json(['password' => $password]);
    }
}
