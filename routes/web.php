<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user', 'UserController@index');
Route::post('/user/add', 'UserController@add');

Route::post('/redis', 'RedisLockController@index');

Route::get('/test', function () {
    echo 'test';
});

Route::post('/test/post', function () {
    echo 'test post';
});
