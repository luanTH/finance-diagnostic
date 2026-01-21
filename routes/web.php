<?php

use App\Http\Controllers\DiagnosticController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LoginController;

// Rotas de Autenticação
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard Protegido
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/leads/{lead}', [AdminController::class, 'show'])->name('leads.show');
    Route::get('/leads/pdf/{lead}', [DiagnosticController::class, 'pdfView'])->name('pdfView');
    Route::get('/reenviar-email/{lead}', [DiagnosticController::class, 'reenviarEmail'])->name('reenviarEmail');
});

Route::get('/', [DiagnosticController::class, 'index'])->name('diagnostic.index');
Route::get('/pj', [DiagnosticController::class, 'index'])->name('diagnostic.indexPj');
Route::prefix('diagnostico')->group(function () {
    Route::post('/enviar', [DiagnosticController::class, 'store'])->name('diagnostic.store');
});
