<?php

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Api\BuyersDataApi;
use App\Http\Controllers\Api\BuyersProfileApi;
use App\Http\Controllers\Api\NotificationsController;
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
Route::get('/notifications', [NotificationsController::class, 'getNotifications']);
Route::get('/notifications/read/{id}', [NotificationsController::class, 'readNotification']);
Route::delete('/notifications/{id}', [NotificationsController::class, 'deleteNotification']);
Route::post('/notifications/settoken', [NotificationsController::class, 'setToken']);

Route::get('/models/{brandID}',  [BuyersDataApi::class, 'models']);
Route::get('/brands', [BuyersDataApi::class, 'brands']);
Route::get('/offers/requests/history', [OffersApiController::class, 'getRequestsHistory']);
Route::get('/offers/requests', [OffersApiController::class, 'getBuyerRequests']);
Route::get('/offers/history', [OffersApiController::class, 'getBuyerOffersHistory']);
Route::get('/offers', [OffersApiController::class, 'getBuyerOffers']);
Route::get('/offers/accepted', [OffersApiController::class, 'getBuyerAcceptedOffers']);
Route::post('/offers/accept', [OffersApiController::class, 'acceptOffer']);
Route::post('/submit/request', [OffersApiController::class, 'submitOfferRequest']);
Route::post('/edit/request/{id}', [OffersApiController::class, 'editOfferRequest']);
Route::put('/cancel/request/{id}', [OffersApiController::class, 'cancelRequest']);
Route::get('/user', [BuyersProfileApi::class, 'getUser']);
Route::post('/user', [BuyersProfileApi::class, 'editUser']);

Route::post('/verify/email', [BaseApiController::class, 'verifyMailCode']);
Route::post('/verify/mob', [BaseApiController::class, 'verifyMobCode']);
Route::post('/resend/email', [BaseApiController::class, 'resendMailCode']);
Route::post('/resend/mob1', [BaseApiController::class, 'resendMob1Code']);
Route::post('/resend/mob2', [BaseApiController::class, 'resendMob2Code']);
Route::post('/delete/user', [BaseApiController::class, 'deleteUser']);
