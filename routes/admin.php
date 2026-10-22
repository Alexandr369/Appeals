<?php

use Azuriom\Plugin\Appeals\Controllers\Admin\AppealController;
use Azuriom\Plugin\Appeals\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the plugin's RouteServiceProvider within a
| group using the "web" and "admin-access" middleware groups.
|
| The "/settings" routes must stay registered before the "/{appeal}"
| wildcard route below, otherwise Laravel would match "settings" as an
| appeal id first and return a 404.
|
*/

Route::middleware('can:appeals.settings')->group(function () {
    Route::get('/settings', [SettingController::class, 'show'])->name('settings');
    Route::post('/settings', [SettingController::class, 'save'])->name('settings.save');
});

Route::middleware('can:appeals.view')->group(function () {
    Route::get('/', [AppealController::class, 'index'])->name('index');
    Route::get('/{appeal}', [AppealController::class, 'show'])->name('show');
});

Route::middleware('can:appeals.manage')->group(function () {
    Route::post('/{appeal}', [AppealController::class, 'update'])->name('update');
    Route::delete('/{appeal}', [AppealController::class, 'destroy'])->name('destroy');
});
