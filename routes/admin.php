<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//buyers routes


//offer routes


//cars routes
Route::get('cars/show', 'Catalog\CarsController@home');
Route::get('cars/add', 'Catalog\CarsController@add');
Route::post('cars/images/add', 'Catalog\CarsController@attachImage');
Route::get('cars/images/del/{id}', 'Catalog\CarsController@deleteImage');
Route::get('cars/profile/{id}', 'Catalog\CarsController@profile');
Route::post('cars/update', 'Catalog\CarsController@update');
Route::post('cars/insert', 'Catalog\CarsController@insert');
Route::post('cars/toggle/offer', 'Catalog\CarsController@toggleOffer');
Route::post('cars/toggle/trending', 'Catalog\CarsController@toggleTrending');
Route::get('cars/unlink/accessory/{carID}/{accessoryID}', 'Catalog\CarsController@deleteAccessoryLink');
Route::post('cars/link/accessory', 'Catalog\CarsController@linkAccessory');
Route::post('cars/load/data', 'Catalog\CarsController@loadData');
Route::post('cars/load/accessories', 'Catalog\CarsController@loadAccessories');
Route::post('cars/update/image', 'Catalog\CarsController@editImage');


//Models routes
Route::get('models/show', 'Catalog\ModelsController@home');
Route::get('models/add', 'Catalog\ModelsController@add');
Route::get('models/profile/{id}', 'Catalog\ModelsController@profile');
Route::post('models/update', 'Catalog\ModelsController@update');
Route::post('models/insert', 'Catalog\ModelsController@insert');
Route::get('models/toggle/main/{id}', 'Catalog\ModelsController@toggleMain');
Route::get('models/toggle/active/{id}', 'Catalog\ModelsController@toggleActive');
Route::post('models/add/image', 'Catalog\ModelsController@attachImage');
Route::post('models/update/image', 'Catalog\ModelsController@editImage');
Route::get('models/image/delete/{id}', 'Catalog\ModelsController@delImage');
Route::post('models/add/color', 'Catalog\ModelsController@attachColor');
Route::post('models/update/color', 'Catalog\ModelsController@editColor');
Route::get('models/color/delete/{id}', 'Catalog\ModelsController@delColor');

//Accessories routes
Route::get('accessories/show', 'Catalog\AccessoriesController@home');
Route::get('accessories/edit/{id}', 'Catalog\AccessoriesController@edit');
Route::post('accessories/update', 'Catalog\AccessoriesController@update');
Route::post('accessories/insert', 'Catalog\AccessoriesController@insert');

//Types routes
Route::get('types/show', 'Catalog\CarTypesController@home');
Route::get('types/edit/{id}', 'Catalog\CarTypesController@edit');
Route::post('types/update', 'Catalog\CarTypesController@update');
Route::post('types/insert', 'Catalog\CarTypesController@insert');
Route::get('types/toggle/{id}', 'Catalog\CarTypesController@toggle');
Route::get('types/delete/{id}', 'Catalog\CarTypesController@delete');

//Brands routes
Route::get('brands/show', 'Catalog\BrandsController@home');
Route::get('brands/edit/{id}', 'Catalog\BrandsController@edit');
Route::post('brands/update', 'Catalog\BrandsController@update');
Route::post('brands/insert', 'Catalog\BrandsController@insert');
Route::get('brands/toggle/{id}', 'Catalog\BrandsController@toggle');
Route::get('brands/delete/{id}', 'Catalog\BrandsController@delete');


//Dashboard users
Route::get("dash/users/all", 'Users\DashUsersController@index');
Route::post("dash/users/insert", 'Users\DashUsersController@insert');
Route::get("dash/users/edit/{id}", 'Users\DashUsersController@edit');
Route::post("dash/users/update", 'Users\DashUsersController@update');

Route::get('/', 'HomeController@admin')->name('home');
Route::get('/home', 'HomeController@admin')->name('home');

