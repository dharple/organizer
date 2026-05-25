<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\BoxModelController;
use App\Http\Controllers\BulkController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/about', [IndexController::class, 'about'])->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('home');

    // Box
    Route::get('/box/showAll', [BoxController::class, 'index'])->name('box.index');
    Route::get('/box/recent', [BoxController::class, 'recent'])->name('box.recent');
    Route::get('/box/new', [BoxController::class, 'create'])->name('box.create');
    Route::post('/box', [BoxController::class, 'store'])->name('box.store');
    Route::get('/box/search', [BoxController::class, 'search'])->name('box.search');
    Route::get('/box/search/id/{id}', [BoxController::class, 'show'])->name('box.show')->where('id', '\d+');
    Route::get('/box/search/location/{id}', [BoxController::class, 'searchByLocation'])->name('box.search.location')->where('id', '\d+');
    Route::get('/box/search/model/{id}', [BoxController::class, 'searchByModel'])->name('box.search.model')->where('id', '\d+');
    Route::get('/box/{id}', [BoxController::class, 'edit'])->name('box.edit')->where('id', '\d+');
    Route::post('/box/{id}', [BoxController::class, 'update'])->name('box.update')->where('id', '\d+');

    // Box Model
    Route::get('/box/model/showAll', [BoxModelController::class, 'index'])->name('box-model.index');
    Route::get('/box/model/new', [BoxModelController::class, 'create'])->name('box-model.create');
    Route::post('/box/model', [BoxModelController::class, 'store'])->name('box-model.store');
    Route::get('/box/model/{id}', [BoxModelController::class, 'edit'])->name('box-model.edit')->where('id', '\d+');
    Route::post('/box/model/{id}', [BoxModelController::class, 'update'])->name('box-model.update')->where('id', '\d+');

    // Location
    Route::get('/location/showAll', [LocationController::class, 'index'])->name('location.index');
    Route::get('/location/new', [LocationController::class, 'create'])->name('location.create');
    Route::post('/location', [LocationController::class, 'store'])->name('location.store');
    Route::get('/location/{id}', [LocationController::class, 'edit'])->name('location.edit')->where('id', '\d+');
    Route::post('/location/{id}', [LocationController::class, 'update'])->name('location.update')->where('id', '\d+');

    // Bulk
    Route::get('/export', [BulkController::class, 'export'])->name('bulk.export');
    Route::post('/export', [BulkController::class, 'exportSubmit'])->name('bulk.export.submit');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
