<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersApiController extends Controller
{
    function authMe(Request $request) {
        $data = $request->only('email', 'password');
        if (Auth::attempt($data)) {
            $this->setNewToken = $this->getToken(100);
            $user = User::find($request->user()->id);
            $user->api_token = $this->setNewToken;
            $user->save();
            return $this->answer();
        } else
            return $this->answer('', 'Incorrect input data');
    }

    function getCurrentUser(Request $request) {
        $user = $this->user;
        if (!$user) {
            return $this->answer('', 'You are not logged in');
        } else {
            return $this->answer($user);
        }
    }

    function createUser(Request $request) {
        $user = new User();

        $user->name= $request->name;
        $user->surname = $request->surname;
        $user->email= $request->email;
        $user->password = Hash::make($request->password);
        $user->api_token = $this->getToken(100);

        $this->setNewToken = $user->api_token;

        if ($user->save())
            return $this->answer();
        else
            return $this->answer('',1);
    }

    function logout() {
        $this->user->api_token=false;
        $this->user->save();
        return $this->answer();
    }

    function getUsers($page=1, $pageSize=false) {
        if (!$pageSize) $pageSize = env('DEF_PAGE_SIZE');
        $offset = (($page-1)*$pageSize);
        $totalUsers = User::whereNotIn('id', [$this->user->id])->count();
        $users = UserResource::collection(User::whereNotIn('id', [$this->user->id])->orderBy('id', 'desc')->skip($offset)->take($pageSize)->get());
        return $this->answer(['totalUsers'=>$totalUsers, 'page'=>$page, 'pageSize'=>$pageSize, 'users'=>$users]);
    }

    function getUser($user) {
        $user = User::find($user);
        if ($user)
            return $this->answer(new UserResource($user));
        else
            return $this->answer('','not found');
    }

    function updateUser($surname, $status) {
        if ((strlen($surname)<33 && strlen($surname)>0) && (strlen($status)<=100 && strlen($status)>0)) {
            User::where('id', $this->user->id)->update(['surname'=>$surname, 'status'=>$status]);
            return $this->answer();
        } else
            return $this->answer('','Input data error');
    }
}
