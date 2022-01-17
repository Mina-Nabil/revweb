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
| This file shall be used only by authentication function for both Sellers and Buyers
*/

Route::post('/seller/register', "Api\SellersProfileApi@register");
Route::post('/seller/check/email', "Api\SellersProfileApi@isEmailTaken");
Route::post('/seller/check/phone', "Api\SellersProfileApi@isPhoneTaken");

Route::post('/buyer/register', "Api\BuyersProfileApi@register");

Route::post('/seller/login', "Api\SellersProfileApi@login");

Route::post('/buyer/login', "Api\BuyersProfileApi@login");
