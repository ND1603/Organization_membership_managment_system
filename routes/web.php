<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\organAdminController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Auth\GoogleController;

use App\Http\Controllers\Auth\OtpController;

use App\Http\Controllers\TelebirrController;

// Telebirr notify — must be CSRF exempt (Telebirr calls this server-to-server)
Route::post('/telebirr/notify', [TelebirrController::class, 'notify'])->name('telebirr.notify');

// Telebirr return URL — no auth needed as Telebirr redirects here
Route::get('/telebirr/return', [TelebirrController::class, 'returnUrl'])->name('telebirr.return');

// Mock pages — local testing only
Route::get('/telebirr/mock', [TelebirrController::class, 'mockPage'])->name('telebirr.mock');
Route::post('/telebirr/mock-pay', [TelebirrController::class, 'mockPay'])->name('telebirr.mock.pay');

use App\Http\Controllers\FaydaController;

// Fayda ID verification routes
Route::middleware('auth')->group(function () {
    Route::get('/fayda',          [FaydaController::class, 'status'])->name('fayda.status');
    Route::get('/fayda/redirect', [FaydaController::class, 'redirect'])->name('fayda.redirect');
    Route::get('/fayda/callback', [FaydaController::class, 'callback'])->name('fayda.callback');
});

// Mock Fayda login page — no auth needed
Route::get('/fayda/mock',       [FaydaController::class, 'mockPage'])->name('fayda.mock');
Route::post('/fayda/mock-login',[FaydaController::class, 'mockLogin'])->name('fayda.mock.login');


// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/telebirr/pay',  [TelebirrController::class, 'create'])->name('telebirr.create');
    Route::post('/telebirr/pay', [TelebirrController::class, 'initiate'])->name('telebirr.initiate');
});


Route::get('/verify-otp',  [OtpController::class, 'show'])->name('otp.show');
Route::post('/verify-otp', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('/resend-otp', [OtpController::class, 'resend'])->name('otp.resend');

Route::get('/', [HomeController::class, 'index']);

Route::get('/about', [HomeController::class, 'about'])->name('guest.about');
Route::get('/service', [HomeController::class, 'service'])->name('guest.service');
Route::get('/events', [HomeController::class, 'event'])->name('guest.events');
Route::get('/blogs', [HomeController::class, 'blog'])->name('guest.blogs');
Route::get('/contact', [HomeController::class, 'contact'])->name('guest.contact');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::get('/home', [HomeController::class, 'redirect'])->middleware('auth', 'verified');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
   Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/verify', [App\Http\Controllers\PaymentVerificationController::class, 'create'])
        ->name('payment-verification.create');
    Route::post('/payment/verify', [App\Http\Controllers\PaymentVerificationController::class, 'store'])
        ->name('payment-verification.store');
    Route::get('/payment/verify/{paymentVerification}', [App\Http\Controllers\PaymentVerificationController::class, 'show'])
        ->name('payment-verification.show');
});

    Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/payments', [App\Http\Controllers\PaymentVerificationController::class, 'adminIndex'])
        ->name('admin.payments.index');
    Route::post('/payments/{paymentVerification}/approve', [App\Http\Controllers\PaymentVerificationController::class, 'approve'])
        ->name('admin.payments.approve');
    Route::post('/payments/{paymentVerification}/reject', [App\Http\Controllers\PaymentVerificationController::class, 'reject'])
        ->name('admin.payments.reject');

     Route::get('/custom-attributes',                    [App\Http\Controllers\Admin\CustomAttributeController::class, 'index'])->name('admin.custom-attributes.index');
    Route::post('/custom-attributes',                   [App\Http\Controllers\Admin\CustomAttributeController::class, 'store'])->name('admin.custom-attributes.store');
    Route::post('/custom-attributes/{customAttribute}/toggle',  [App\Http\Controllers\Admin\CustomAttributeController::class, 'toggle'])->name('admin.custom-attributes.toggle');
    Route::delete('/custom-attributes/{customAttribute}',       [App\Http\Controllers\Admin\CustomAttributeController::class, 'destroy'])->name('admin.custom-attributes.destroy');
    });    
});

// Payment Routes
Route::get('/payment', [organAdminController::class, 'payment'])->name('payment');
Route::get('/payment/{plan_id}', [organAdminController::class, 'payment']);

Route::get('/organ', [AdminController::class, 'organ']);
Route::get('/orgAdmin', [AdminController::class, 'orgAdmin']);
Route::post('/orgadmin_upload', [AdminController::class, 'orgadmin_upload']);
Route::get('/members', [AdminController::class, 'members']);
Route::get('/organadmin', [AdminController::class, 'organadmin']);
Route::get('/payments', [AdminController::class, 'payments']);
Route::get('/payments/{paymentVerification}/invoice', [App\Http\Controllers\PaymentVerificationController::class, 'downloadInvoice'])->name('admin.payments.invoice');

Route::get('/addorgan', [AdminController::class, 'muaz']);
Route::post('/uploadorgan', [AdminController::class, 'uploadorgan']);
Route::get('/editorgan/{id}', [AdminController::class, 'editorgan']);
Route::post('/updateorgan/{id}', [AdminController::class, 'updateorgan']);
Route::get('/deleteorgan/{id}', [AdminController::class, 'deleteorgan']);

Route::post('/uploadpayment', [organAdminController::class, 'uploadpayment'])
    ->middleware('auth')
    ->name('uploadpayment');

Route::get('/editmembers/{id}', [AdminController::class, 'editmembers']);
Route::post('/updatemember/{id}', [AdminController::class, 'updatemember']);
Route::get('/deletemembers/{id}', [AdminController::class, 'deletemembers']);

Route::get('/member1', [organAdminController::class, 'member']);
Route::get('/event', [organAdminController::class, 'event']);
Route::get('/blog', [organAdminController::class, 'blog']);
Route::get('/upgrade', [PlanController::class, 'ShowUpgradePlan'])->name('organAdmin.plans.upgrade');
Route::post('/upgrade', [PlanController::class, 'UpgradePlan']);

Route::get('/sidebar nav', [organAdminController::class, 'sidebar']);
Route::get('/edit_profile/{id}', [organAdminController::class, 'editprofile']);
Route::post('/updateprofile/{id}', [organAdminController::class, 'updateprofile']);

Route::get('/addmember', [organAdminController::class, 'addmember']);
Route::post('/upload_member', [organAdminController::class, 'upload']);
Route::get('/edit/{id}', [organAdminController::class, 'edit']);
Route::post('/editmember/{id}', [organAdminController::class, 'editmember']);
Route::get('/deletemember/{id}', [organAdminController::class, 'deletemember']);

Route::post('/uploadevent', [organAdminController::class, 'uploadevent']);
Route::get('/editevent/{id}', [organAdminController::class, 'editevent']);
Route::post('/updateevent/{id}', [organAdminController::class, 'updateevent']);
Route::get('/deleteevent/{id}', [organAdminController::class, 'deleteevent']);

Route::post('/uploadblog', [organAdminController::class, 'uploadblog']);
Route::get('/editblog/{id}', [organAdminController::class, 'editblog']);
Route::post('/updateblog/{id}', [organAdminController::class, 'updateblog']);
Route::get('/deleteblog/{id}', [organAdminController::class, 'deleteblog']);

Route::get('/sidebar', [MemberController::class, 'sidebar1']);
Route::get('/event1', [MemberController::class, 'event12']);
Route::get('/profile', [MemberController::class, 'profile']);
Route::middleware('auth')->get('/my-payments/{paymentVerification}/invoice', [App\Http\Controllers\PaymentVerificationController::class, 'downloadInvoice'])->name('payments.invoice.download');