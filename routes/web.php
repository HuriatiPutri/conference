<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\RegistrationController; 
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('home.dashboard');
});

Route::get('/home/test', function () {
    return view('home.test');
});

Route::get('/login', function () {
    return view('auth.login');
});

//conference
Route::get('/home/conference/create', function () {
    return view('home.conference.create');
});
Route::get('/home/conference/', [ConferenceController::class, 'index'])->name('home.conference.index');
Route::resource('conference', ConferenceController::class);

//home
Route::get('/home', function () {
    return view('public.home');
})->name('home');

//registration
Route::get('/registration/{conference}', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/registration/{conference}', [RegistrationController::class, 'store'])->name('registration.store');
Route::get('/registration/detail/{audience_id}', [RegistrationController::class, 'show'])->name('registration.show');

//payment
Route::post('/registration/checkout/token', [PaymentController::class, 'getSnapToken'])->name('payment.getSnapToken');

Route::post('/paypal/pay', [PaypalController::class, 'createTransaction'])->name('paypal.pay');
Route::get('/paypal/success', [PaypalController::class, 'captureTransaction'])->name('paypal.success');
Route::get('/paypal/cancel', [PaypalController::class, 'cancelTransaction'])->name('paypal.cancel');


//audience
Route::resource('audience', AudienceController::class);
Route::get('/home/audience/create', [AudienceController::class, 'create'])->name('home.audience.create');
Route::get('/home/audience/', [AudienceController::class, 'index'])->name('home.audience.index');