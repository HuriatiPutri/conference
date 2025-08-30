<?php

use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalController;
use Illuminate\Support\Facades\Route;

// notification
Route::post('/payment/notification', [PaymentController::class, 'handleNotification']);
Route::post('/paypal/webhook', [PaypalController::class, 'handle']);
Route::get('/paypal/status/{orderId}', [PaypalController::class, 'checkPaypalOrder'])->name('paypal.checkPaypalOrder');
