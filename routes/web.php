<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('login', 'HomeController@login')->name('login');
Route::post('login', 'HomeController@authenticate')->name('login');
Route::get('logout', 'HomeController@logout')->name('logout');
