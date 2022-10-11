<?php

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

Route::get('/', function () {
    return view('welcome');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('wallets', \App\Http\Livewire\Wallets::class)->name('wallets');
    Route::get('transactions/{walletId}', \App\Http\Livewire\Transactions::class)->name('transactions');
    Route::get('transfer', \App\Http\Livewire\Transfer::class)->name('transfer');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
