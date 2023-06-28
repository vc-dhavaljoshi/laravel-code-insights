<?php

use Vcian\LaravelCodeInsights\Http\Controllers\CodeDocController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/generate-package', [CodeDocController::class, 'index'])->name('code_docs');