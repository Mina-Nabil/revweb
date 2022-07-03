<?php

use App\Http\Controllers\Api\BuyersDataApi;
use App\Http\Controllers\Api\BuyersProfileApi;
use App\Http\Controllers\Api\OffersApiController;
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

Route::get('/models/{brandID}',  [BuyersDataApi::class, 'models']);
Route::get('/brands', [BuyersDataApi::class, 'brands']);
Route::get('/offers/requests', [OffersApiController::class, 'getBuyerRequests']);
Route::get('/offers', [OffersApiController::class, 'getBuyerOffers']);
Route::post('/submit/request', [OffersApiController::class, 'submitOfferRequest']);
Route::post('/edit/request', [OffersApiController::class, 'editOfferRequest']);
Route::put('/cancel/request/{id}', [OffersApiController::class, 'cancelRequest']);
Route::get('/user', [BuyersProfileApi::class, 'getUser']);
Route::post('/user', [BuyersProfileApi::class, 'editUser']);
