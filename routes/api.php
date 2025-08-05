<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\RegistrationController; 
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\PaymentController;

//notification
Route::post('/payment/notification', [PaymentController::class, 'handleNotification']);

Route::get('/ping', fn() => response()->json(['pong' => true]));
