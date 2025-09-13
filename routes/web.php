<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AudienceController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\KeyNoteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParallelSessionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

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
    // conference
    Route::get('/home/conference/create', function () {
        return view('home.conference.create');
    });
    Route::get('/home/conference/', [ConferenceController::class, 'index'])->name('home.conference.index');
    Route::resource('conference', ConferenceController::class);
    Route::get('/home/conference/setting-certificate/{conference}', [ConferenceController::class, 'settingCertificate'])->name('home.conference.setting-certificate');
    Route::post('/home/conference/store-template/{conference}', [ConferenceController::class, 'storeTemplate'])->name('conference.storeTemplate');
    Route::post('/home/conference/store-template/position/{conference}', [ConferenceController::class, 'storeTemplatePosition'])->name('conference.storeTemplatePosition');

    // audience
    Route::get('/home/audience/', [AudienceController::class, 'index'])->name('home.audience.index');
    Route::resource('audience', AudienceController::class);
    // Route::get('/home/audience/create', [AudienceController::class, 'create'])->name('home.audience.create');
    // Route::post('/home/audience/', [AudienceController::class, 'update'])->name('audience.update');
    Route::get('/home/audience/download/{audience}', [AudienceController::class, 'downloadCertificate'])->name('home.audience.download');

    //keynote
    Route::get('/home/keynote/{conference}', [KeyNoteController::class, 'keynoteList'])->name('home.keynote.index');
    // Activity Log
    Route::get('/home/activity-logs', [ActivityLogController::class, 'index'])->name('home.activity-log.index');
    Route::resource('activity-logs', ActivityLogController::class);
});

// home
Route::get('/home', function () {
    return view('public.home');
})->name('home');

// registration
Route::get('/registration/{conference}', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/registration/{conference}', [RegistrationController::class, 'store'])->name('registration.store');
Route::get('/registration/detail/{audience_id}', [RegistrationController::class, 'show'])->name('registration.show');

// payment
Route::post('/registration/checkout/token', [PaymentController::class, 'getSnapToken'])->name('payment.getSnapToken');

Route::post('/paypal/pay', [PaypalController::class, 'createTransaction'])->name('paypal.pay');
Route::get('/paypal/success', [PaypalController::class, 'captureTransaction'])->name('paypal.success');
Route::get('/paypal/cancel', [PaypalController::class, 'cancelTransaction'])->name('paypal.cancel');
Route::post('/paypal/webhook', [PaypalController::class, 'handle']);

// keynote
Route::get('/keynote/{conference}', [KeyNoteController::class, 'index'])->name('keynote.index');
Route::post('/keynote/{conference}', [KeyNoteController::class, 'store'])->name('keynote.store');
Route::get('/keynote/detail/{keyNote}', [KeyNoteController::class, 'show'])->name('keynote.show');

// parallel session
Route::get('/parallel-session/{conference}', [ParallelSessionController::class, 'index'])->name('parallel-session.index');
Route::post('/parallel-session/{conference}', [ParallelSessionController::class, 'store'])->name('parallel-session.store');
Route::get('/parallel-session/detail/{parallelSession}', [ParallelSessionController::class, 'show'])->name('parallel-session.show');

// certificate
Route::get('/certificate', [CertificateController::class, 'index'])->name('certificate.index');
Route::post('/certificate', [CertificateController::class, 'store'])->name('certificate.store');

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
