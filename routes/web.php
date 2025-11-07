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
