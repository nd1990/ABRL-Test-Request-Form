<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminBackupController;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminImportExportController;
use App\Http\Controllers\AdminInvoiceController;
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
Route::post('/quotation/preview', [PublicQuotationController::class, 'preview'])->name('quotation.preview');
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

        Route::middleware('admin.permission:dashboard.view')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        });

        Route::middleware('admin.permission:quotations.view')->group(function () {
            Route::get('/quotations', [AdminQuotationController::class, 'index'])->name('quotations.index');
            Route::get('/quotations/accepted', [AdminQuotationController::class, 'accepted'])->middleware('admin.permission:quotations.view_accepted')->name('quotations.accepted');
            Route::get('/quotations/data', [AdminQuotationController::class, 'data'])->name('quotations.data');
            Route::get('/quotations/{quotation}', [AdminQuotationController::class, 'show'])->name('quotations.show');
            Route::get('/quotations/{quotation}/pdf', [AdminQuotationController::class, 'pdf'])->name('quotations.pdf');
            Route::get('/quotations/{quotation}/documents/msds', [AdminQuotationController::class, 'documentMsds'])->name('quotations.documents.msds');
            Route::get('/quotations/{quotation}/documents/other/{index}', [AdminQuotationController::class, 'documentOther'])->whereNumber('index')->name('quotations.documents.other');
            Route::get('/quotations/{quotation}/edit', [AdminQuotationController::class, 'edit'])->middleware('admin.permission:quotations.edit')->name('quotations.edit');
            Route::put('/quotations/{quotation}', [AdminQuotationController::class, 'update'])->middleware('admin.permission:quotations.edit')->name('quotations.update');
            Route::post('/quotations/{quotation}/status', [AdminQuotationController::class, 'updateStatus'])->middleware('admin.permission:quotations.edit')->name('quotations.status');
            Route::post('/quotations/{quotation}/resend', [AdminQuotationController::class, 'resend'])->middleware('admin.permission:quotations.send_email')->name('quotations.resend');
            Route::post('/quotations/{quotation}/duplicate', [AdminQuotationController::class, 'duplicate'])->middleware('admin.permission:quotations.create')->name('quotations.duplicate');
            Route::delete('/quotations/{quotation}', [AdminQuotationController::class, 'destroy'])->middleware('admin.permission:quotations.delete')->name('quotations.destroy');
        });

        Route::middleware('admin.permission:invoices.view')->group(function () {
            Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
            Route::get('/invoices/data', [AdminInvoiceController::class, 'data'])->name('invoices.data');
            Route::get('/invoices/create/{quotation}', [AdminInvoiceController::class, 'create'])->middleware('admin.permission:invoices.create')->name('invoices.create');
            Route::post('/invoices/create/{quotation}', [AdminInvoiceController::class, 'store'])->middleware('admin.permission:invoices.create')->name('invoices.store');
            Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{invoice}/pdf', [AdminInvoiceController::class, 'pdf'])->name('invoices.pdf');
            Route::delete('/invoices/{invoice}', [AdminInvoiceController::class, 'destroy'])->middleware('admin.permission:invoices.delete')->name('invoices.destroy');
        });

        Route::middleware('admin.permission:lab_tests.view')->group(function () {
            Route::get('/services', [AdminServicesController::class, 'index'])->name('services.index');
            Route::get('/services/data', [AdminServicesController::class, 'data'])->name('services.data');
            Route::post('/services', [AdminServicesController::class, 'store'])->middleware('admin.permission:lab_tests.create')->name('services.store');
            Route::put('/services/{labTest}', [AdminServicesController::class, 'update'])->middleware('admin.permission:lab_tests.edit')->name('services.update');
            Route::delete('/services/bulk', [AdminServicesController::class, 'bulkDestroy'])->middleware('admin.permission:lab_tests.delete')->name('services.bulk-destroy');
            Route::delete('/services/{labTest}', [AdminServicesController::class, 'destroy'])->middleware('admin.permission:lab_tests.delete')->name('services.destroy');
            Route::post('/services/sync', [AdminServicesController::class, 'sync'])->middleware('admin.permission:lab_tests.create')->name('services.sync');
            Route::post('/services/import', [AdminServicesController::class, 'import'])->middleware('admin.permission:lab_tests.create')->name('services.import');
        });

        Route::middleware('admin.permission:quotations.view')->group(function () {
            Route::get('/export/quotations', [AdminImportExportController::class, 'quotationsExport'])->name('export.quotations');
        });

        Route::middleware('admin.permission:invoices.view')->group(function () {
            Route::get('/export/invoices', [AdminImportExportController::class, 'invoicesExport'])->name('export.invoices');
        });

        // Change password (submitted from the Settings page; any logged-in admin)
        Route::post('/settings/password', [AdminUserController::class, 'changePassword'])->name('password.change.submit');

        // User management
        Route::middleware('admin.permission:users.view')->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->middleware('admin.permission:users.create')->name('users.create');
            Route::post('/users', [AdminUserController::class, 'store'])->middleware('admin.permission:users.create')->name('users.store');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->middleware('admin.permission:users.edit')->name('users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->middleware('admin.permission:users.edit')->name('users.update');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->middleware('admin.permission:users.delete')->name('users.destroy');
        });

        // Backups
        Route::middleware('admin.permission:backups.view')->group(function () {
            Route::get('/backups', [AdminBackupController::class, 'index'])->name('backups.index');
            Route::post('/backups', [AdminBackupController::class, 'store'])->middleware('admin.permission:backups.create')->name('backups.store');
            Route::post('/backups/settings', [AdminBackupController::class, 'saveSettings'])->middleware('admin.permission:backups.create')->name('backups.settings');
            Route::get('/backups/{backup}/download', [AdminBackupController::class, 'download'])->name('backups.download');
            Route::post('/backups/{backup}/restore', [AdminBackupController::class, 'restore'])->middleware('admin.permission:backups.restore')->name('backups.restore');
            Route::delete('/backups/{backup}', [AdminBackupController::class, 'destroy'])->middleware('admin.permission:backups.delete')->name('backups.destroy');
        });

        // Settings
        Route::middleware('admin.permission:settings.view')->group(function () {
            Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [AdminSettingsController::class, 'update'])->middleware('admin.permission:settings.edit')->name('settings.update');
        });
    });
});