<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\DashboardController;

// ======================
// AUTHENTIFICATION
// ======================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth:api');
});

// ======================
// ROUTES PROTÉGÉES (JWT)
// ======================
Route::middleware('auth:api')->group(function () {

    // Patients
    Route::apiResource('patients', PatientController::class);
    Route::get('patients/search', [PatientController::class, 'index']);

    // Médecins
    Route::apiResource('medecins', MedecinController::class);
    Route::get('medecins/search', [MedecinController::class, 'index']);

    // Rendez-vous
    Route::apiResource('rdv', RendezVousController::class);

    // Dashboard statistiques
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
});