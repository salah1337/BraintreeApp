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

Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
Route::prefix('/customer')->group( function(){
    Route::get('/create', 'CustomerController@create')->middleware('auth');
    Route::post('/create', 'CustomerController@store')->middleware('auth');
});
Route::prefix('/subscription')->group( function(){
    Route::get('/create', 'SubscriptionController@create')->middleware('auth');
    Route::post('/create', 'SubscriptionController@store')->middleware('auth');
});