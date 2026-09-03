<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminImportExportController;
use App\Http\Controllers\AdminQuotationController;
use App\Http\Controllers\AdminServicesController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\LabTestController;
use App\Http\Controllers\PublicQuotationController;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicQuotationController::class, 'index'])->name('home');

Route::get('/quotation', [PublicQuotationController::class, 'index'])->name('quotation.index');
Route::get('/quotation/services', [PublicQuotationController::class, 'services'])->name('quotation.services');
Route::get('/lab-tests/options', [LabTestController::class, 'options'])->name('lab-tests.options');
Route::post('/quotation', [PublicQuotationController::class, 'store'])->name('quotation.store');
Route::get('/quotation/success/{quotation}', [PublicQuotationController::class, 'success'])->name('quotation.success');
Route::get('/quotation/download/{quotation}', [PublicQuotationController::class, 'download'])->name('quotation.download');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Forgot / Reset password (public - outside auth group)
    Route::get('/forgot-password', [AdminPasswordResetController::class, 'showForgotForm'])->name('password.forgot');
    Route::post('/forgot-password', [AdminPasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password', [AdminPasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AdminPasswordResetController::class, 'resetPassword'])->name('password.reset.submit');

    Route::middleware('admin.auth')->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/quotations', [AdminQuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/data', [AdminQuotationController::class, 'data'])->name('quotations.data');
        Route::get('/quotations/{quotation}', [AdminQuotationController::class, 'show'])->name('quotations.show');
        Route::get('/quotations/{quotation}/edit', [AdminQuotationController::class, 'edit'])->name('quotations.edit');
        Route::put('/quotations/{quotation}', [AdminQuotationController::class, 'update'])->name('quotations.update');
        Route::get('/quotations/{quotation}/pdf', [AdminQuotationController::class, 'pdf'])->name('quotations.pdf');
        Route::post('/quotations/{quotation}/resend', [AdminQuotationController::class, 'resend'])->name('quotations.resend');
        Route::post('/quotations/{quotation}/duplicate', [AdminQuotationController::class, 'duplicate'])->name('quotations.duplicate');
        Route::delete('/quotations/{quotation}', [AdminQuotationController::class, 'destroy'])->name('quotations.destroy');

        Route::get('/services', [AdminServicesController::class, 'index'])->name('services.index');
        Route::get('/services/data', [AdminServicesController::class, 'data'])->name('services.data');
        Route::post('/services', [AdminServicesController::class, 'store'])->name('services.store');
        Route::put('/services/{labTest}', [AdminServicesController::class, 'update'])->name('services.update');
        Route::delete('/services/bulk', [AdminServicesController::class, 'bulkDestroy'])->name('services.bulk-destroy');
        Route::delete('/services/{labTest}', [AdminServicesController::class, 'destroy'])->name('services.destroy');
        Route::post('/services/sync', [AdminServicesController::class, 'sync'])->name('services.sync');
        Route::post('/services/import', [AdminServicesController::class, 'import'])->name('services.import');

        Route::get('/export/quotations', [AdminImportExportController::class, 'quotationsExport'])->name('export.quotations');

        // Change password (any logged-in admin)
        Route::get('/change-password', [AdminUserController::class, 'showChangePassword'])->name('password.change');
        Route::post('/change-password', [AdminUserController::class, 'changePassword'])->name('password.change.submit');

        // User management (master admin only)
        Route::middleware('admin.master')->group(function () {
            Route::resource('users', AdminUserController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        // Settings (master admin only)
        Route::middleware('admin.master')->group(function () {
            Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        });
    });
});