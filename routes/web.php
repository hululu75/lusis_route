<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RouteFileController;
use App\Http\Controllers\RouteMatchController;
use App\Http\Controllers\DeltaController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\XmlImportExportController;
use App\Http\Controllers\MatchConditionController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Projects
Route::resource('projects', ProjectController::class);

// Services
Route::resource('services', ServiceController::class);

// Route Files
Route::resource('route-files', RouteFileController::class);

// Matches
Route::resource('matches', RouteMatchController::class);

// Deltas
Route::resource('deltas', DeltaController::class);

// Rules
Route::resource('rules', RuleController::class);

// Routes
Route::resource('routes', RouteController::class);

// Route priority/ordering (for drag-and-drop)
Route::post('/routes/reorder', [RouteController::class, 'reorder'])->name('routes.reorder');

// XML Import/Export
Route::get('/xml/import', [XmlImportExportController::class, 'showImportForm'])->name('xml.import');
Route::post('/xml/import', [XmlImportExportController::class, 'import'])->name('xml.import.process');
Route::get('/xml/export', [XmlImportExportController::class, 'showExportForm'])->name('xml.export');
Route::post('/xml/export', [XmlImportExportController::class, 'export'])->name('xml.export.process');

// Match Conditions API (for inline editing)
Route::post('/match-conditions', [MatchConditionController::class, 'store'])->name('match-conditions.store');
Route::put('/match-conditions/{condition}', [MatchConditionController::class, 'update'])->name('match-conditions.update');
Route::delete('/match-conditions/{condition}', [MatchConditionController::class, 'destroy'])->name('match-conditions.destroy');
