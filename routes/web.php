<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\TestIntegrationController;
use App\Http\Controllers\GoogleDebugController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\LanguageController;

Route::get('/', function () {
    return view('welcome');
});

// Language switching
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/reuniones', [MeetingController::class, 'index'])->name('reuniones.index');
    Route::post('/reuniones', [MeetingController::class, 'store'])->middleware('check.meeting.limit')->name('reuniones.store');

    Route::get('/reuniones/{meeting}', [MeetingController::class, 'show'])->name('reuniones.show');

    Route::post('/meetings/{meeting}/send-email', [MeetingController::class, 'sendEmail'])->name('meetings.send-email');
    
    // Integrations
    Route::get('/integrations', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::post('/integrations', [IntegrationController::class, 'store'])->name('integrations.store');
    Route::delete('/integrations/{integration}', [IntegrationController::class, 'destroy'])->name('integrations.destroy');
    Route::get('/integrations/notion/databases', [IntegrationController::class, 'listNotionDatabases'])->name('integrations.notion.databases');
    Route::post('/integrations/notion/databases/save', [IntegrationController::class, 'saveNotionDatabase'])->name('integrations.notion.database.save');

    // API Token para extensión Chrome / Google Meet
    Route::post('/api-token/regenerate', [IntegrationController::class, 'regenerateApiToken'])->name('api-token.regenerate');

    // Test integrations
    Route::post('/test/notion', [App\Http\Controllers\TestIntegrationController::class, 'testNotion'])->name('test.notion');
    Route::post('/test/slack', [App\Http\Controllers\TestIntegrationController::class, 'testSlack'])->name('test.slack');
    Route::post('/test/google-sheets', [App\Http\Controllers\TestIntegrationController::class, 'testGoogleSheets'])->name('test.google-sheets');
    Route::post('/test/all-integrations', [App\Http\Controllers\TestIntegrationController::class, 'testAll'])->name('test.all-integrations');
    Route::post('/tasks/{task}/send', [App\Http\Controllers\TaskController::class, 'sendTask'])->name('tasks.send');
    Route::post('/meetings/{meeting}/send-all-tasks', [MeetingController::class, 'sendAllTasks'])->name('meetings.send-all-tasks');
    // Google Debug
    Route::get('/debug/google', [App\Http\Controllers\GoogleDebugController::class, 'debug'])->name('debug.google');
    Route::get('/debug/google/spreadsheets', [App\Http\Controllers\GoogleDebugController::class, 'listSpreadsheets'])->name('debug.google.spreadsheets');


    // Rutas de stripe
    Route::post('/checkout', [StripeController::class, 'checkout'])->name('checkout');
    Route::get('/checkout/success', [StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])->name('checkout.cancel');
    Route::post('/subscription/cancel', [StripeController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('/subscription', [StripeController::class, 'manage'])->name('subscription.manage');
    Route::get('/subscription/invoice/{invoiceId}', [StripeController::class, 'downloadInvoice'])->name('subscription.invoice.download');
    Route::post('/subscription/change-plan', [StripeController::class, 'changePlan'])->name('subscription.change-plan');
    Route::post('/subscription/resume', [StripeController::class, 'resumeSubscription'])->name('subscription.resume');

});


Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');



// Páginas legales
Route::get('/privacidad', [App\Http\Controllers\LegalController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terminos', [App\Http\Controllers\LegalController::class, 'termsOfService'])->name('terms.service');
Route::get('/cookies', [App\Http\Controllers\LegalController::class, 'cookiePolicy'])->name('cookie.policy');

// Ruta para el contacto
Route::post('/contacto', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

// OAuth Google Routes
Route::get('/oauth/google', [GoogleController::class, 'redirect'])->name('oauth.google.redirect');
Route::get('/oauth/google/callback', [GoogleController::class, 'callback'])->name('oauth.google.callback');
Route::post('/oauth/google/store', [GoogleController::class, 'store'])->name('oauth.google.store');
Route::get('/oauth/google/sheets', [GoogleController::class, 'listSheets'])->name('oauth.google.sheets');
Route::post('/oauth/google/sheets/save', [GoogleController::class, 'saveSheet'])->name('oauth.google.sheets.save');

require __DIR__.'/auth.php';