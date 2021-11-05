<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::middleware("auth")->get('/', 'HomeController@admin')->name('home');
Route::middleware("auth")->get('home', 'HomeController@admin')->name('home');
Route::get('login', 'HomeController@login')->name('login');
Route::post('login', 'HomeController@authenticate')->name('login');
Route::get('logout', 'HomeController@logout')->name('logout');
