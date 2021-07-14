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

use App\Http\Controllers\ContactController;

Route::get('/auth/redirect', 'App\Http\Controllers\AuthController@authorize');

Route::get('/client/create', 'App\Http\Controllers\ContactController@create')->name('contact.create');
Route::post('/client/store', 'App\Http\Controllers\ContactController@store')->name('contact.store');
