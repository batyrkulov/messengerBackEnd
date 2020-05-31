<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



// for all
Route::get('me', 'UsersApiController@getCurrentUser');
Route::get('isFree/{object}/{value}', 'GeneralController@isFree');

Route::middleware(['OnlyForAuthed', 'CorrectingData'])->group(function () {
    Route::delete('auth', 'UsersApiController@logout');
    Route::get('users/{page?}/{pageSize?}', 'UsersApiController@getUsers');
    Route::get('user/{userId}', 'UsersApiController@getUser');
    Route::get('contacts/{page?}/{pageSize?}', 'ContactsApiController@getContacts');
    Route::get('messages/{userId}/{page?}/{pageSize?}', 'MessagesApiController@getMessages');
    Route::post('message', 'MessagesApiController@send');
    Route::put('me/{surname}/{status}', 'UsersApiController@updateUser');
});

Route::middleware(['OnlyForGuests'])->group(function () {
    Route::post('me', 'UsersApiController@createUser');
    Route::post('auth', 'UsersApiController@authMe');
});
