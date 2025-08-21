<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;


Route::get('/settings.run', [SettingController::class, 'run'])->name('settings.run');
Route::get('/settings.translate', [SettingController::class, 'translate'])->name('settings.translate');
Route::get('/settings.log', [LogViewerController::class, 'index'])->name('settings.log');
Route::get('locale/{locale}', [SettingController::class, 'changeLanguage'])->name('change_language');

Route::get('/find-users', [UserController::class, 'find_users'])->name('find_users');

Route::group(['middleware' => ['guest']], function() {

    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
});

Route::group(['middleware' => ['auth', 'checkstatus']], function() {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard-search', [DashboardController::class, 'search'])->name('dashboard.search');

    Route::get('/transaction-index', [TransactionController::class, 'index'])->name('transactions.index')->middleware('permission:transaction');
    Route::get('/transaction-search', [TransactionController::class, 'search'])->name('transactions.search')->middleware('permission:transaction');
    Route::post('/transaction-store', [TransactionController::class, 'store'])->name('transactions.store')->middleware('permission:transaction.create');
    Route::post('/transaction-destroy', [TransactionController::class, 'destroy'])->name('transactions.destroy')->middleware('permission:transaction.destroy');

    Route::get('/profile-edit/{user_cid}', [UserController::class, 'profileEdit'])->name('users.profile_edit');
    Route::put('/profile-update', [UserController::class, 'profileUpdate'])->name('users.profile_update');
});

Route::group(['middleware' => ['auth', 'checkstatus', 'role:superadministrator']], function () {

    Route::get('/user-index', [UserController::class, 'index'])->name('users.index')->middleware('permission:user');
    Route::get('/user-search', [UserController::class, 'search'])->name('users.search')->middleware('permission:user');
    Route::post('/user-store', [UserController::class, 'store'])->name('users.store')->middleware('permission:user.create');
    Route::get('/user-show/{user_cid}', [UserController::class, 'show'])->name('users.show')->middleware('permission:user');
    Route::get('/user-show-search', [UserController::class, 'show_search'])->name('users.show_search')->middleware('permission:user');
    Route::put('/user-update', [UserController::class, 'update'])->name('users.update')->middleware('permission:user.edit');
    Route::post('/user-destroy', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:user.destroy');

    Route::get('/setting-index', [SettingController::class, 'index'])->name('settings.index')->middleware('permission:setting');
    Route::put('/setting-update', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:setting.edit');

    Route::get('/superadministrator-profile-edit/{user_cid}', [UserController::class, 'superadministratorProfileEdit'])->name('superadministrators.profile_edit');
    Route::put('/superadministrator-profile-update', [UserController::class, 'superadministratorProfileUpdate'])->name('superadministrators.profile_update');
});

Route::get('/route-cache', function () {
    Artisan::call('route:cache');
    return 'Routes cache cleared';
});

Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return 'Config cache cleared';
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'Application cache cleared';
});

Route::get('/view-clear', function () {
    Artisan::call('view:clear');
    return 'View cache cleared';
});

Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    return 'All cache cleared';
});

require __DIR__.'/auth.php';
