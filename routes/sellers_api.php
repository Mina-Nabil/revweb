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

//Showroom Management routes
Route::get('/showroom', "ShowroomProfileApi@getShowroom");
Route::get('/get/banking', "ShowroomProfileApi@getBankInfo");
Route::post('/set/banking', "ShowroomProfileApi@setBankInfo");
Route::delete('/delete/banking', "ShowroomProfileApi@deleteBankInfo");
Route::post('/create/showroom', "ShowroomProfileApi@createShowroom");
Route::get('/cities', "ShowroomProfileApi@getCities");

//catalog functions
Route::get('/get/catalog', "ShowroomCatalogApiController@getCatalog");
Route::get('/remove/car', "ShowroomCatalogApiController@removeCar");
Route::get('/remove/year', "ShowroomCatalogApiController@removeYear");
Route::post('/set/brands', "ShowroomCatalogApiController@setBrands");
Route::get('/get/all/brands', "ShowroomCatalogApiController@getAllBrands");
Route::get('/get/models/{brandID}', "ShowroomCatalogApiController@getModelsByBrand");
Route::get('/get/cars/{modelID}', "ShowroomCatalogApiController@getCarsByModel");
Route::get('/get/colors/{modelID}', "ShowroomCatalogApiController@getColorsByModel");
Route::get('/get/my/brands', "ShowroomCatalogApiController@getShowroomBrands");
Route::get('/get/carpool', "ShowroomCatalogApiController@getCatalogCarPool");
Route::post('/add/car', "ShowroomCatalogApiController@addCarsToCatalog");


//profile functions
Route::get('/user', "SellersProfileApi@getUser");
Route::post('/update', "SellersProfileApi@updateSellerData");
