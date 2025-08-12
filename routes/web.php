<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\RegistrationController; 
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\LoginController;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::get('/register', [LoginController::class, 'showRegistrationForm'])->name('register')->middleware('role:superadmin');;
// Route::post('/register', [LoginController::class, 'register']); 

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
    
    Route::get('/home', function () {
        return view('home.dashboard');
    });
    
    Route::get('/home/test', function () {
        return view('home.test');
    });
    //conference
    Route::get('/home/conference/create', function () {
        return view('home.conference.create');
    });
    Route::get('/home/conference/', [ConferenceController::class, 'index'])->name('home.conference.index');
    Route::resource('conference', ConferenceController::class);

    //audience
    Route::resource('audience', AudienceController::class);
    Route::get('/home/audience/create', [AudienceController::class, 'create'])->name('home.audience.create');
    Route::get('/home/audience/', [AudienceController::class, 'index'])->name('home.audience.index');

});

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


Route::get('/check-role', function () {
    $user = auth()->user();

    if (!$user) {
        return 'Tidak ada user yang login';
    }

    return [
        'user' => $user->name ?? $user->email,
        'roles' => $user->getRoleNames(), // daftar role
        'permissions' => $user->getAllPermissions()->pluck('name'), // daftar permission
    ];
})->middleware('auth'); // pastikan user sudah login