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
Route::get('/', 'ProductWebController@index')->name('products.index');

Route::middleware('auth')->group(function () {
    Route::post('/cart/add', 'CartController@add')->name('cart.add');
    Route::get('/cart', 'CartController@index')->name('cart.index');
    Route::post('/cart/remove/{productId}', 'CartController@remove')->name('cart.remove');
    Route::get('/cart/checkout', 'OrderWebController@createFromCart')->name('cart.checkout');
    Route::post('/cart/checkout', 'OrderWebController@storeFromCart')->name('cart.checkout.store');

    Route::get('/checkout', 'OrderWebController@create')->name('orders.create');
    Route::post('/checkout', 'OrderWebController@store')->name('orders.store');

    Route::get('/orders', 'OrderWebController@index')->name('orders.index');
    Route::get('/orders/{orderNumber}', 'OrderWebController@show')->name('orders.show');
    Route::post('/orders/{orderNumber}/status', 'OrderWebController@updateStatus')->name('orders.updateStatus');
});
Route::get('/home', 'HomeController@index')->name('home');


Auth::routes();

