<?php

use App\Http\Controllers\ClientDocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkspaceCountryController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/workspace/country', [WorkspaceCountryController::class, 'update'])->name('workspace.country');

    Route::get('/clients/{client}/documents/{docKey}', [ClientDocumentController::class, 'show'])
        ->where('docKey', '[a-z0-9_]+')
        ->middleware('throttle:60,1')
        ->name('clients.documents.show');

    Route::get('/clients/{client}/photo', [ClientDocumentController::class, 'photo'])
        ->middleware('throttle:60,1')
        ->name('clients.photo.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
