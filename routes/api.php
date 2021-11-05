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

Route::post('/seller/register', "SellersProfileApi@register");

Route::post('/buyer/register', "BuyersProfileApi@register");

Route::post('/seller/login', "SellersProfileApi@login");

Route::post('/buyer/login', "BuyersProfileApi@login");
