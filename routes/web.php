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
    Route::get('/', 'CustomerController@show')->middleware('auth');
    Route::get('/show', 'CustomerController@show')->middleware('auth');
    Route::get('/create', 'CustomerController@create')->middleware('auth');
    Route::post('/create', 'CustomerController@store')->middleware('auth');
    Route::get('/edit', 'CustomerController@edit')->middleware('auth');
    Route::patch('/update', 'CustomerController@update')->middleware('auth');
    Route::get('/delete', 'CustomerController@destroy')->middleware('auth');
});
Route::prefix('/subscription')->group( function(){
    Route::get('/create', 'SubscriptionController@create')->middleware('auth');
    Route::get('/store/{planId}', 'SubscriptionController@store')->middleware('auth');
    Route::get('/edit/{id}', 'SubscriptionController@edit')->middleware('auth');
    Route::patch('/update/{id}', 'SubscriptionController@update')->middleware('auth');
    Route::get('/show/{id}', 'SubscriptionController@show')->middleware('auth');
    Route::get('/cancel/{id}', 'SubscriptionController@cancel')->middleware('auth');
});
