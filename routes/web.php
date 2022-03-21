<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [BookingController::class, 'index']);
Route::get('/getevent', [BookingController::class, 'getEvent']);
Route::post('/booking', [BookingController::class, 'createBooking'])->name('booking');
Route::post('/delete', [BookingController::class, 'deleteEvent']);
Route::post('/theater-details', [BookingController::class, 'theaterDetails']);