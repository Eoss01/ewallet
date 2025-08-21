<?php

use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallets/{wallet}')->group(function () {

    Route::get('/show_balance', [WalletController::class, 'balance'])->name('wallets.balance');
    Route::get('/transactions', [WalletController::class, 'transactions'])->name('wallets.transactions');
    Route::post('/deposit', [WalletController::class, 'deposit'])->name('wallets.deposit');
    Route::post('/withdraw', [WalletController::class, 'withdraw'])->name('wallets.withdraw');
});
