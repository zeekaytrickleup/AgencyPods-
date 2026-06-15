<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\GoalSectionController;
use App\Http\Controllers\PodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WeeklyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/weekly', [WeeklyController::class, 'index'])->name('weekly.index');
    Route::post('/weekly-tasks', [WeeklyController::class, 'store'])->name('weekly.store');
    Route::put('/weekly-tasks/{task}', [WeeklyController::class, 'update'])->name('weekly.update');
    Route::patch('/weekly-tasks/{task}/toggle', [WeeklyController::class, 'toggle'])->name('weekly.toggle');
    Route::delete('/weekly-tasks/{task}', [WeeklyController::class, 'destroy'])->name('weekly.destroy');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
        Route::get('/reports/pods/{pod}/pdf', [ReportController::class, 'downloadPodPdf'])->name('reports.pod.pdf');

        // User management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/pods', [PodController::class, 'store'])->name('pods.store');
        Route::delete('/pods/{pod}', [PodController::class, 'destroy'])->name('pods.destroy');
    });

    // Managers manage their own team
    Route::middleware('role:pod_manager')->group(function () {
        Route::get('/team', [TeamController::class, 'index'])->name('team.index');
        Route::post('/team', [TeamController::class, 'store'])->name('team.store');
        Route::delete('/team/{user}', [TeamController::class, 'destroy'])->name('team.destroy');
    });

    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::put('/goals/{goal}', [GoalController::class, 'update'])->name('goals.update');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');

    Route::put('/sections/{section}', [GoalSectionController::class, 'update'])->name('sections.update');

    Route::post('/sections/{section}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::get('/attachments/{attachment}/preview', [AttachmentController::class, 'preview'])->name('attachments.preview');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
