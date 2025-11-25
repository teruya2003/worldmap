<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 国関連のルート
    Route::get('/countries/{country}', [CountryController::class, 'show'])->name('countries.show');
    Route::post('/countries/{country}/status', [CountryController::class, 'updateStatus'])->name('countries.status.update');
    Route::post('/countries/{country}/photos', [CountryController::class, 'storePhoto'])->name('countries.photos.store');
    Route::delete('/photos/{photo}', [CountryController::class, 'deletePhoto'])->name('photos.delete');
});

require __DIR__.'/auth.php';
