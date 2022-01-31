<?php

use App\Http\Controllers\SmsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function(){
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
        
    Route::get('/send-message', function () {
        return Inertia::render('SendSms');
    })->name('send-message');    

    Route::get('/send-batch-message', function () {
        return Inertia::render('SendBatchSms');
    })->name('send-batch-message');
    
    Route::get('/get-success-data', [SmsController::class, 'getSuccessRates'])->name('get-success-data');
    Route::get('/get-token', [SmsController::class, 'getToken'])->name('get-token');
    Route::get('/get-balance', [SmsController::class, 'getBalance'])->name('get-balance');
    Route::post('/send-sms', [SmsController::class, 'sendSms'])->name('send-sms');
    Route::post('/send-batch-sms', [SmsController::class, 'sendBatchSms'])->name('send-batch-sms');
});
