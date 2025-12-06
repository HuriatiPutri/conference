<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ConferencesController;
use App\Http\Controllers\Admin\AudiencesController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\KeynoteController;
use App\Http\Controllers\ParallelSessionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Admin\KeynoteManagementController;
use App\Http\Controllers\Admin\ParallelSessionManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Admin\LettersOfApprovalController;
use App\Http\Controllers\Admin\LoaVolumeManagementController;
use App\Http\Controllers\Admin\DashboardController;
use Inertia\Inertia;

// Public routes
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/detail/{conference:public_id}', [LandingController::class, 'detail'])->name('landing.detail');

// Route::get('/', function () {
//     return Inertia::render('Home', [
//         'name' => 'Putri',
//     ]);
// });

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Conferences
    Route::get('conferences', [ConferencesController::class, 'index'])
        ->name('conferences');

    Route::get('conferences/{conference}/show', [ConferencesController::class, 'show'])
        ->name('conferences.show');

    Route::get('conferences/create', [ConferencesController::class, 'create'])
        ->name('conferences.create');

    Route::post('conferences', [ConferencesController::class, 'store'])
        ->name('conferences.store');

    Route::get('conferences/{conference}/edit', [ConferencesController::class, 'edit'])
        ->name('conferences.edit');

    Route::post('conferences/{conference}', [ConferencesController::class, 'update'])
        ->name('conferences.update');

    Route::get('conferences/{conference}/setting', [ConferencesController::class, 'setting'])
        ->name('conferences.setting');

    Route::post('conferences/template/{conference}', [ConferencesController::class, 'uploadCertificate'])
        ->name('conferences.uploadCertificate');

    Route::put('conferences/{conference}/updateSetting', [ConferencesController::class, 'storeSetting'])
        ->name('conferences.updateSetting');

    Route::delete('conferences/{conference}', [ConferencesController::class, 'destroy'])
        ->name('conferences.destroy');

    Route::put('conferences/{conference}/restore', [ConferencesController::class, 'restore'])
        ->name('conferences.restore');

    // Audiences
    Route::get('audiences', [AudiencesController::class, 'index'])
        ->name('audiences');
    
    Route::get('audiences/export', [AudiencesController::class, 'export'])
        ->name('audiences.export');
    
    Route::get('audiences/{audience}/receipt', [AudiencesController::class, 'downloadReceipt'])
        ->name('audiences.receipt');
    
    Route::get('audiences/{audience}/show', [AudiencesController::class, 'show'])
        ->name('audiences.show');
    
    Route::get('audiences/create', [AudiencesController::class, 'create'])
        ->name('audiences.create');
    
    Route::post('audiences', [AudiencesController::class, 'store'])
        ->name('audiences.store');
    
    Route::get('audiences/{audience}/edit', [AudiencesController::class, 'edit'])
        ->name('audiences.edit');
    
    Route::put('audiences/{audience}', [AudiencesController::class, 'update'])
        ->name('audiences.update');
    
    Route::delete('audiences/{audience}', [AudiencesController::class, 'destroy'])
        ->name('audiences.destroy');
    
    Route::put('audiences/{audience}/restore', [AudiencesController::class, 'restore'])
        ->name('audiences.restore');
    
    Route::get('audiences/download/{audience:public_id}', [AudiencesController::class, 'download'])
        ->name('audiences.download');
    
    Route::patch('audiences/{audience}/payment-status', [AudiencesController::class, 'updatePaymentStatus'])
        ->name('audiences.updatePaymentStatus');

    // Admin - Keynote Management
    Route::get('keynotes', [KeynoteManagementController::class, 'index'])->name('keynotes.index');
    Route::get('keynotes/{keynote}', [KeynoteManagementController::class, 'show'])->name('keynotes.show');
    Route::delete('keynotes/{keynote}', [KeynoteManagementController::class, 'destroy'])->name('keynotes.destroy');

    // Admin - Parallel Session Management
    Route::get('parallel-sessions', [ParallelSessionManagementController::class, 'index'])->name('parallel-sessions.index');
    Route::get('parallel-sessions/{parallelSession}', [ParallelSessionManagementController::class, 'show'])->name('parallel-sessions.show');
    Route::delete('parallel-sessions/{parallelSession}', [ParallelSessionManagementController::class, 'destroy'])->name('parallel-sessions.destroy');

    // Letters of Approval routes
    Route::get('letters-of-approval', [LettersOfApprovalController::class, 'index'])->name('letters-of-approval.index');
    Route::get('letters-of-approval/{audience:id}', [LettersOfApprovalController::class, 'show'])->name('letters-of-approval.show');
    Route::get('letters-of-approval/{audience:id}/download-form', [LettersOfApprovalController::class, 'downloadForm'])->name('letters-of-approval.download-form');
    Route::post('letters-of-approval/{audience:id}/update-info', [LettersOfApprovalController::class, 'updateLoaInfo'])->name('letters-of-approval.update-info');
    Route::get('letters-of-approval/{audience:id}/download', [LettersOfApprovalController::class, 'download'])->name('letters-of-approval.download');
    Route::post('letters-of-approval/bulk-download', [LettersOfApprovalController::class, 'bulkDownload'])->name('letters-of-approval.bulk-download');
    Route::patch('letters-of-approval/{audience:id}/status', [LettersOfApprovalController::class, 'updateStatus'])->name('letters-of-approval.update-status');

    // LoA - LoA Volume Management
    Route::prefix('loa')->name('loa.')->group(function () {
        Route::resource('loa-volumes', LoaVolumeManagementController::class);
    });

    // JOIV Article Management - Admin Routes
    Route::prefix('joiv-articles')->name('joiv-articles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\JoivArticleController::class, 'index'])->name('index');
        Route::get('/fee-settings', [\App\Http\Controllers\Admin\JoivArticleController::class, 'feeSettings'])->name('fee-settings');
        Route::post('/fee-settings', [\App\Http\Controllers\Admin\JoivArticleController::class, 'updateFee'])->name('fee-settings.update');
        Route::get('/{joivArticle}', [\App\Http\Controllers\Admin\JoivArticleController::class, 'show'])->name('show');
        Route::patch('/{joivArticle}/payment-status', [\App\Http\Controllers\Admin\JoivArticleController::class, 'updatePaymentStatus'])->name('updatePaymentStatus');
        Route::get('/{joivArticle}/download-paper', [\App\Http\Controllers\Admin\JoivArticleController::class, 'downloadPaper'])->name('downloadPaper');
        Route::get('/{joivArticle}/download-payment-proof', [\App\Http\Controllers\Admin\JoivArticleController::class, 'downloadPaymentProof'])->name('downloadPaymentProof');
        Route::get('/{joivArticle}/download-receipt', [\App\Http\Controllers\Admin\JoivArticleController::class, 'downloadReceipt'])->name('downloadReceipt');
        Route::get('/export/excel', [\App\Http\Controllers\Admin\JoivArticleController::class, 'export'])->name('export');
        Route::delete('/{joivArticle}', [\App\Http\Controllers\Admin\JoivArticleController::class, 'destroy'])->name('destroy');
        Route::put('/{joivArticle}/restore', [\App\Http\Controllers\Admin\JoivArticleController::class, 'restore'])->name('restore');
    });
});

// Registration - Public Access (No Auth Middleware)
Route::get('/registration/{conference:public_id}', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/registration/{conference:public_id}', [RegistrationController::class, 'store'])->name('registration.store');
Route::get('/registration/{conference:public_id}/payment', [RegistrationController::class, 'payment'])->name('registration.payment');
Route::post('/registration/{conference:public_id}/payment', [RegistrationController::class, 'processPayment'])->name('registration.processPayment');
Route::get('/registration/{conference:public_id}/details/{audience:public_id}', [RegistrationController::class, 'paymentDetails'])->name('registration.details');
Route::get('/registration/{conference:public_id}/success/{audience:public_id}', [RegistrationController::class, 'success'])->name('registration.success');

// PayPal Routes
Route::get('/registration/{conference:public_id}/paypal/return', [RegistrationController::class, 'paypalReturn'])->name('registration.paypal.return');
Route::get('/registration/{conference:public_id}/paypal/cancel', [RegistrationController::class, 'paypalCancel'])->name('registration.paypal.cancel');

// Keynote and Parallel Session Routes - Public Access (No Auth Middleware)
Route::get('/keynote/{conference:public_id}', [KeynoteController::class, 'create'])->name('keynote.create');
Route::post('/keynote/{conference:public_id}', [KeynoteController::class, 'store'])->name('keynote.store');
Route::get('/keynote/{conference:public_id}/success', [KeynoteController::class, 'success'])->name('keynote.success');

Route::get('/parallel-session/{conference:public_id}', [ParallelSessionController::class, 'create'])->name('parallel-session.create');
Route::post('/parallel-session/{conference:public_id}', [ParallelSessionController::class, 'store'])->name('parallel-session.store');
Route::get('/parallel-session/{conference:public_id}/success', [ParallelSessionController::class, 'success'])->name('parallel-session.success');

// Certificate Download - Public Access (No Auth Middleware)
Route::get('/certificate/download', [CertificateController::class, 'downloadOrShow'])->name('certificate.download');
Route::post('/certificate/download', [CertificateController::class, 'download'])->name('certificate.download.post');

// JOIV Registration - Public Access (No Auth Middleware)
Route::get('/joiv/registration', [\App\Http\Controllers\JoivRegistrationController::class, 'index'])->name('joiv.registration');
Route::post('/joiv/registration', [\App\Http\Controllers\JoivRegistrationController::class, 'store'])->name('joiv.registration.store');
Route::get('/joiv/registration/{registration:public_id}/detail', [\App\Http\Controllers\JoivRegistrationController::class, 'details'])->name('joiv.registration.details');
Route::get('/joiv/registration/{registration:public_id}/payment', [\App\Http\Controllers\JoivRegistrationController::class, 'payment'])->name('joiv.payment');
Route::post('/joiv/registration/{registration:public_id}/payment', [\App\Http\Controllers\JoivRegistrationController::class, 'processPayment'])->name('joiv.payment.process');
Route::get('/joiv/registration/{registration:public_id}/payment/complete', [\App\Http\Controllers\JoivRegistrationController::class, 'paymentComplete'])->name('joiv.payment.complete');
Route::get('/joiv/registration/{registration:public_id}/paypal/success', [\App\Http\Controllers\JoivRegistrationController::class, 'paypalSuccess'])->name('joiv.paypal.success');
Route::get('/joiv/registration/{registration:public_id}/paypal/cancel', [\App\Http\Controllers\JoivRegistrationController::class, 'paypalCancel'])->name('joiv.paypal.cancel');