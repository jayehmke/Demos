<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'SessionController@create');

Route::group(array('before'=>'auth'), function() {
	Route::get('dashboard', 'FlavorController@dashboard');
	Route::get('flavors/getneeds', 'FlavorController@getNeeds');
});

Route::group(array('before'=>'auth'), function() { 
	Route::resource('bottles', 'BottleController');
});

Route::group(array('before'=>'auth'), function() { 
	Route::resource('manufacturers', 'ManufacturerController');
});

Route::group(array('before'=>'auth'), function() { 
	Route::resource('flavors', 'FlavorController');
});

Route::group(array('before'=>'auth'), function() { 
	Route::resource('ingredients', 'IngredientController');
});


Route::group(array('before'=>'auth'), function() {
	Route::get('ingredients/{id}/getcount', 'IngredientController@getcount');
});

Route::group(array('before'=>'auth'), function() {
	Route::get('flavors/{id}/getingredients', 'FlavorController@getIngredients');
});

Route::get('login', 'SessionController@create');

Route::group(array('before'=>'auth'), function() { 
	Route::get('logout', 'SessionController@destroy');
});

Route::group(array('before'=>'auth'), function() { 
	Route::get('flavors/{id}/{qty}/make', 'FlavorController@make');
});


Route::resource('sessions', 'SessionController', ['only' => ['index', 'create', 'destroy', 'store']]);


Route::group(array('before'=>'auth'), function() {   
    Route::resource('brands', 'BrandController');
});

//Route::resource('brands', 'BrandController');

Route::group(array('before'=>'auth'), function() { 
	Route::resource('users', 'UserController');
});