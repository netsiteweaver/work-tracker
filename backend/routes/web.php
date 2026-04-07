<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProjectController::class, 'index'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/sync/projects-fingerprint', [ProjectController::class, 'syncFingerprint'])->name('projects.sync-fingerprint');

    // Admin landing page
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    
    // Admin projects management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/projects', [ProjectController::class, 'admin'])->name('projects');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::post('/projects/update-order', [ProjectController::class, 'updateOrder'])->name('projects.update-order');
        
        // Admin settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/remove-background', [SettingsController::class, 'removeBackground'])->name('settings.remove-background');
        
        // Admin users management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
