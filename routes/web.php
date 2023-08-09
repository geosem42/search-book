<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware([
	'auth:sanctum',
	config('jetstream.auth_session'),
	'verified',
])->group(function () {
	Route::get('/dashboard', function () {
		return Inertia::render('Dashboard');
	})->name('dashboard');
	Route::post('/upload', [HomeController::class, 'upload'])->name('upload');
	Route::get('/search', [HomeController::class, 'search'])->name('search');
	Route::get('/documents', [HomeController::class, 'documents'])->name('documents');

	Route::get('/csrf-token', function () {
		return response()->json(['csrfToken' => csrf_token()]);
	});
});
