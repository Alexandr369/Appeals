<?php

use Azuriom\Plugin\Appeals\Controllers\AppealController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the plugin's RouteServiceProvider within a
| group using the "web" middleware group and the "appeals" prefix.
|
*/

Route::middleware('auth')->group(function () {
    Route::middleware('can:appeals.submit')->group(function () {
        Route::get('/', [AppealController::class, 'index'])->name('index');
        Route::get('/create', [AppealController::class, 'create'])->name('create');
        Route::post('/', [AppealController::class, 'store'])->name('store');
        Route::get('/{appeal}', [AppealController::class, 'show'])->name('show');
    });

    Route::middleware('can:appeals.edit')->group(function () {
        Route::get('/{appeal}/edit', [AppealController::class, 'edit'])->name('edit');
        Route::put('/{appeal}', [AppealController::class, 'update'])->name('update');
    });
});
