<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/orders', 'OrderController@store');
Route::get('/orders/{orderNumber}', 'OrderController@show');
Route::post('/webhooks/payment', 'PaymentWebhookController@handle');