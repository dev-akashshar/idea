<?php

declare(strict_types=1);

use App\Http\Controllers\IdeaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistereduserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/ideas');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistereduserController::class, 'create']);
    Route::post('/register', [RegistereduserController::class, 'store']);

    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy']);

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/edit', [ProfileController::class, 'update']);

    Route::get('/ideas', [IdeaController::class, 'index'])->name('idea.index');
    Route::post('/ideas', [IdeaController::class, 'store'])->name('idea.store');
    Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('idea.show')->can('view', 'idea');
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('idea.update')->can('update', 'idea');
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('idea.destroy')->can('delete', 'idea');

    Route::patch('/steps/{step}', [StepController::class, 'update'])->name('step.update')->can('update', 'step');
});
