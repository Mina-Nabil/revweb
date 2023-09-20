<?php

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Api\CustomersApiController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\OffersApiController;
use App\Http\Controllers\Api\SellersProfileApi;
use App\Http\Controllers\Api\ShowroomCatalogApiController;
use App\Http\Controllers\Api\ShowroomProfileApi;
use App\Http\Controllers\Api\SubscriptionsController;
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

//Customers routes
Route::get('/customers', [CustomersApiController::class, 'getCustomers']);

//Offer routes
Route::post('/submit/offer', [OffersApiController::class, 'submitNewOffer']);
Route::get('/offerrequests', [OffersApiController::class, 'getShowroomCompatibleOfferRequests']);
Route::get('/offers/pending', [OffersApiController::class, 'getShowroomPendingOffers']);
Route::get('/offers/approved', [OffersApiController::class, 'getShowroomApprovedOffers']);
Route::get('/offers/expired', [OffersApiController::class, 'getShowroomExpiredOffers']);
Route::post('/extend/offer', [OffersApiController::class, 'extendOffer']);
Route::post('/extend/offers', [OffersApiController::class, 'extendAllPendingOffers']);
Route::post('/cancel/offer', [OffersApiController::class, 'cancelOffer']);
Route::post('/offers/document', [OffersApiController::class, 'addDocument']);
Route::delete('/offers/document/{$id}', [OffersApiController::class, 'deleteDoc']);
Route::post('/offers/extra', [OffersApiController::class, 'addExtra']);
Route::delete('/offers/extra/{$id}', [OffersApiController::class, 'deleteExtra']);
Route::get('/offers/{$id}', [OffersApiController::class, 'getOffer']);
Route::get('/offers/{$id}/documents', [OffersApiController::class, 'getOfferDocuments']);
Route::get('/offers/{$id}/extras', [OffersApiController::class, 'getOfferExtras']);

//Showroom Management routes
Route::get('/showroom', [ShowroomProfileApi::class, 'getShowroom']);
Route::get('/get/banking', [ShowroomProfileApi::class, 'getBankInfo']);
Route::post('/set/banking', [ShowroomProfileApi::class, 'setBankInfo']);
Route::delete('/delete/banking', [ShowroomProfileApi::class, 'deleteBankInfo']);
Route::post('/create/showroom', [ShowroomProfileApi::class, 'createShowroom']);
Route::get('/cities', [ShowroomProfileApi::class, 'getCities']);
Route::get('get/team', [ShowroomProfileApi::class, 'getTeam']);
Route::post('/search/sellers', [ShowroomProfileApi::class, 'searchSellers']);
Route::get('/get/invitations', [ShowroomProfileApi::class, 'getJoinRequestsAndInvitations']);
Route::post('/invite/seller', [ShowroomProfileApi::class, 'inviteSellerToShowroom']);
Route::delete('/delete/request', [ShowroomProfileApi::class, 'deleteSellerInvitation']);
Route::post('/accept/seller', [ShowroomProfileApi::class, 'acceptJoinRequest']);

//catalog functions
Route::get('/get/catalog', [ShowroomCatalogApiController::class, 'getCatalog']);
Route::delete('/remove/car', [ShowroomCatalogApiController::class, 'removeCar']);
Route::get('/remove/year', [ShowroomCatalogApiController::class, 'removeYear']);
Route::post('/set/brands', [ShowroomCatalogApiController::class, 'setBrands']);
Route::get('/get/all/brands', [ShowroomCatalogApiController::class, 'getAllBrands']);
Route::get('/get/models/{brandID}', [ShowroomCatalogApiController::class, 'getModelsByBrand']);
Route::get('/get/cars/{modelID}', [ShowroomCatalogApiController::class, 'getCarsByModel']);
Route::get('/get/colors/{modelID}', [ShowroomCatalogApiController::class, 'getColorsByModel']);
Route::get('/get/my/brands', [ShowroomCatalogApiController::class, 'getShowroomBrands']);
Route::get('/get/carpool', [ShowroomCatalogApiController::class, 'getCatalogCarPool']);
Route::post('/add/car', [ShowroomCatalogApiController::class, 'addCarsToCatalog']);

//notifications functions
Route::get('/notifications', [NotificationsController::class, 'getNotifications']);
Route::get('/notifications/read/{id}', [NotificationsController::class, 'readNotification']);
Route::delete('/notifications/{id}', [NotificationsController::class, 'deleteNotification']);
Route::post('/notifications/settoken', [NotificationsController::class, 'setToken']);

//profile functions
Route::get('/user', [SellersProfileApi::class, 'getUser']);
Route::post('/user', [SellersProfileApi::class, 'editUser']);
Route::post('/search/showrooms', [SellersProfileApi::class, 'searchShowrooms']);
Route::get('/get/joinrequests', [SellersProfileApi::class, 'getJoinRequestsAndInvitations']);
Route::post('/submit/join/request', [SellersProfileApi::class, 'submitShowroomJoinRequest']);
Route::post('/accept/invitation', [SellersProfileApi::class, 'acceptShowroomInvitation']);
Route::get('/leave/showroom', [SellersProfileApi::class, 'leaveShowroom']);
Route::delete('/delete/showroom', [SellersProfileApi::class, 'deleteShowroom']);

//subscription API
Route::get('/plans', [SubscriptionsController::class, 'plans']);
Route::get('/limits', [SubscriptionsController::class, 'limits']);
Route::post('/subscriptions', [SubscriptionsController::class, 'addSubscriptions']);


Route::post('/verify/email', [BaseApiController::class, 'verifyMailCode']);
Route::post('/verify/mob', [BaseApiController::class, 'verifyMobCode']);
Route::post('/resend/email', [BaseApiController::class, 'resendMailCode']);
Route::post('/resend/mob1', [BaseApiController::class, 'resendMob1Code']);
Route::post('/resend/mob2', [BaseApiController::class, 'resendMob2Code']);
Route::post('/delete/user', [BaseApiController::class, 'deleteUser']);
