<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\CalendarsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\AuthController;

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

const C_PATH = 'App\Http\Controllers\\';
Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', C_PATH.'AuthController@login');
    Route::post('register', C_PATH.'AuthController@register');
    Route::middleware('auth:api')->post('logout', C_PATH.'AuthController@logout');
    Route::post('password-reset', C_PATH.'AuthController@resetPassword');
    Route::post('password-reset/{token}', C_PATH.'AuthController@newPassword');
});

Route::group(['middleware' => 'auth:api'], function ($router) {
Route::get('users/me', C_PATH.'UsersController@getMe');
Route::get('users/{id}', C_PATH.'UsersController@get');
Route::post('users/avatar', C_PATH.'UsersController@avatar');
Route::patch('users/update', C_PATH.'UsersController@update');
Route::delete('users/delete', C_PATH.'UsersController@delete');

Route::get('calendars', C_PATH.'CalendarsController@getAll');
Route::get('calendars/{id}', C_PATH.'CalendarsController@get');
Route::post('calendars', C_PATH.'CalendarsController@create');
Route::post('calendars/{id}/event', C_PATH.'CalendarsController@createEvent');
Route::post('calendars/{id}/user', C_PATH.'CalendarsController@addUser');
Route::patch('calendars/{id}/user', C_PATH.'CalendarsController@editUser');
Route::patch('calendars/{id}', C_PATH.'CalendarsController@update');
Route::delete('calendars/{id}', C_PATH.'CalendarsController@delete');

Route::get('events', C_PATH.'EventsController@getAll');
Route::get('events/{id}', C_PATH.'EventsController@get');
Route::post('events/{id}/user', C_PATH.'EventsController@addUser');
Route::patch('events/{id}', C_PATH.'EventsController@update');
Route::delete('events/{id}', C_PATH.'EventsController@delete');
});
