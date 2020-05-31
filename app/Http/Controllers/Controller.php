<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cookie;

class Controller extends BaseController
{
    protected $user = false;
    protected $setNewToken = false;

    public function __construct()
    {
        $token = Cookie::get('api_token');
        if ($token) {
            $this->user = User::where('api_token', Cookie::get('api_token'))->first();
        }
    }

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function answer($data='', $error='') {
        $response = response()->json([
            'errorCode' => ($error==='' ? 0 : 1),
            'error'=>$error,
            'data'=>$data
        ], 201);

        if ($this->setNewToken)
            $response->cookie('api_token', $this->setNewToken, 9999999);

        return $response;
    }

    protected function getToken($length){
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        return $token;
    }
}
