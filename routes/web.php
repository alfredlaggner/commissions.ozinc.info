<?php

	/*
	|--------------------------------------------------------------------------
	| Web Routes
	|--------------------------------------------------------------------------
	|
	| Here is where you can register web routes for your application. These
	| routes are loaded by the RouteServiceProvider within a group which
	| contains the "web" middleware group. Now create something great!
	|
	*/

	if (App::environment('local')) {
		URL::forceScheme('http');
	}
	Route::get('/home', function () {
		return view('home');
	});
	Route::get('/welcome', function () {
		return view('welcome');
	});

	Auth::routes();

	Route::get('/', 'CommissionController@index')->name('home');
	Route::post('/commission_calc', 'CommissionController@calcCommissions');
	Route::post('/saleorder_calc', 'CommissionController@calcCommissionsPerSalesOrder');
	Route::post('/init_calc', 'DevelopController@calcCommissions');
	Route::get('go-home', array('as' => 'go-home', 'uses' => 'CommissionController@index'));
	Route::post('view-so/{order_id}', array('as' => 'view_so', 'uses' => 'CommissionController@displaySalesOrder'));

	Route::get('/init_calc', 'DevelopController@calcCommissions');
	Route::get('/comm', 'DevelopController@allCommissions');
	Route::get('donutchart/{customer_id}/{customer_name}/{salesperson}/{month}', array('as' => 'donutchart', 'uses' => 'CommissionController@commissionsPerCustomerBrand'));

	Route::post('/calc_region', 'DevelopController@calcRegions');
