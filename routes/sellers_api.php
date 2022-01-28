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
*/

//Offer routes
Route::get('/offerrequests', "Api\OffersApiController@getShowroomCompatibleOfferRequests");
Route::get('/offers/pending', "Api\OffersApiController@getShowroomPendingOffers");
Route::get('/offers/approved', "Api\OffersApiController@getShowroomApprovedOffers");
Route::get('/offers/expired', "Api\OffersApiController@getShowroomExpiredOffers");

//Showroom Management routes
Route::get('/showroom', "Api\ShowroomProfileApi@getShowroom");
Route::get('/get/banking', "Api\ShowroomProfileApi@getBankInfo");
Route::post('/set/banking', "Api\ShowroomProfileApi@setBankInfo");
Route::delete('/delete/banking', "Api\ShowroomProfileApi@deleteBankInfo");
Route::post('/create/showroom', "Api\ShowroomProfileApi@createShowroom");
Route::get('/cities', "Api\ShowroomProfileApi@getCities");
Route::get('get/team', "Api\ShowroomProfileApi@getTeam");
Route::post('/search/sellers', "Api\ShowroomProfileApi@searchSellers");
Route::get('/get/invitations', "Api\ShowroomProfileApi@getJoinRequestsAndInvitations");
Route::post('/invite/seller', "Api\ShowroomProfileApi@inviteSellerToShowroom");
Route::delete('/delete/request', "Api\ShowroomProfileApi@deleteSellerInvitation");
Route::post('/accept/seller', "Api\ShowroomProfileApi@acceptJoinRequest");

//catalog functions
Route::get('/get/catalog', "Api\ShowroomCatalogApiController@getCatalog");
Route::delete('/remove/car', "Api\ShowroomCatalogApiController@removeCar");
Route::get('/remove/year', "Api\ShowroomCatalogApiController@removeYear");
Route::post('/set/brands', "Api\ShowroomCatalogApiController@setBrands");
Route::get('/get/all/brands', "Api\ShowroomCatalogApiController@getAllBrands");
Route::get('/get/models/{brandID}', "Api\ShowroomCatalogApiController@getModelsByBrand");
Route::get('/get/cars/{modelID}', "Api\ShowroomCatalogApiController@getCarsByModel");
Route::get('/get/colors/{modelID}', "Api\ShowroomCatalogApiController@getColorsByModel");
Route::get('/get/my/brands', "Api\ShowroomCatalogApiController@getShowroomBrands");
Route::get('/get/carpool', "Api\ShowroomCatalogApiController@getCatalogCarPool");
Route::post('/add/car', "Api\ShowroomCatalogApiController@addCarsToCatalog");


//profile functions
Route::get('/user', "Api\SellersProfileApi@getUser");
Route::post('/update', "Api\SellersProfileApi@updateSellerData");
Route::post('/search/showrooms', "Api\SellersProfileApi@searchShowrooms");
Route::get('/get/joinrequests', "Api\SellersProfileApi@getJoinRequestsAndInvitations");
Route::post('/submit/join/request', "Api\SellersProfileApi@submitShowroomJoinRequest");
Route::post('/accept/invitation', "Api\SellersProfileApi@acceptShowroomInvitation");
Route::get('/leave/showroom', "Api\SellersProfileApi@leaveShowroom");
Route::delete('/delete/showroom', "Api\SellersProfileApi@deleteShowroom");
