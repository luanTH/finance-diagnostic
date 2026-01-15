<?php

use App\Http\Controllers\DiagnosticController;
use Illuminate\Support\Facades\Route;

Route::prefix('diagnostico')->group(function () {
    Route::get('/', [DiagnosticController::class, 'index'])->name('diagnostic.index');
    Route::post('/enviar', [DiagnosticController::class, 'store'])->name('diagnostic.store');
    Route::get('/show/{id}', [DiagnosticController::class, 'show'])->name('diagnostic.show');
    Route::get('/sucesso/{lead}', [DiagnosticController::class, 'success'])->name('diagnostic.success');
});
