<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    function isFree($object, $value) {
        if ($object=='email') {
            $res = DB::table('users')->where('email', $value)->value('id');
            if ($res) return $this->answer('', 'Not free');
            else return $this->answer();
        }
    }
}
