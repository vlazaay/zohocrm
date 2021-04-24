<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DealController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ZohooController;
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
    return view('welcome');
});

// Listing deal route
Route::get('deal', [TaskController::class, 'index'])->name('deal');

// Add deal in ZOHO
Route::get('zohocrmauth', [ZohooController::class, 'auth'])->name('zohocrmauth');
Route::get('callback', [ZohooController::class, 'store'])->name('callback');
