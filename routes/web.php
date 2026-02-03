<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\MaintenanceReportController;
use App\Http\Controllers\EntwicklerController;
use App\Http\Controllers\AdminSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Routes accessibles par tous les utilisateurs authentifiés (developer + manager)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reports - Lecture, téléchargement et envoi par mail (developer + manager)
    Route::get('/reports', [MaintenanceReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [MaintenanceReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/download', [MaintenanceReportController::class, 'download'])->name('reports.download');
    Route::post('/reports/{report}/resend', [MaintenanceReportController::class, 'resend'])->name('reports.resend');
    Route::post('/reports/{report}/send-email', [MaintenanceReportController::class, 'sendEmail'])->name('reports.send-email');
    Route::post('/reports/{report}/send-teams', [MaintenanceReportController::class, 'sendTeams'])->name('reports.send-teams');
    Route::post('/reports/{report}/regenerate-pdf', [MaintenanceReportController::class, 'regeneratePdf'])->name('reports.regenerate-pdf');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes réservées aux développeurs uniquement
Route::middleware(['auth', 'verified', 'role:developer'])->group(function () {
    // Clients - CRUD complet
    Route::resource('clients', ClientController::class);

    // Websites - CRUD complet
    Route::resource('websites', WebsiteController::class);

    // Entwickler - CRUD complet
    Route::resource('entwicklers', EntwicklerController::class);

    // Maintenance - Création, édition, mise à jour, finalisation
    Route::get('/websites/{website}/maintenance/create', [MaintenanceReportController::class, 'create'])->name('maintenance.create');
    Route::post('/websites/{website}/maintenance', [MaintenanceReportController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{report}/edit', [MaintenanceReportController::class, 'edit'])->name('maintenance.edit');
    Route::put('/maintenance/{report}', [MaintenanceReportController::class, 'update'])->name('maintenance.update');
    Route::post('/maintenance/{report}/complete', [MaintenanceReportController::class, 'complete'])->name('maintenance.complete');
    Route::delete('/reports/{report}', [MaintenanceReportController::class, 'destroy'])->name('reports.destroy');

    // Administration
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::put('/admin/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/admin/settings/delete-logo', [AdminSettingsController::class, 'deleteLogo'])->name('admin.settings.delete-logo');
});

require __DIR__.'/auth.php';
