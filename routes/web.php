<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageBuilderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index'); // recommended path
    })->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/builder/create', [PageBuilderController::class, 'create'])
        ->name('builder.create');
    Route::post('/builder', [PageBuilderController::class, 'store'])
        ->name('builder.store');
    Route::get('/builder/{id}/edit', [PageBuilderController::class, 'edit'])
        ->name('builder.edit');
    Route::post('/builder/{id}/update', [PageBuilderController::class, 'update'])
        ->name('builder.update');
    Route::get('/builder', [PageBuilderController::class, 'index'])
        ->name('pages.index');
    Route::delete('/builder/{id}', [PageBuilderController::class, 'destroy'])->name('builder.destroy');
});
Route::get('/page/{slug}', [PageBuilderController::class, 'view'])
    ->name('builder.page');
Route::get('/page/id/{id}', [PageBuilderController::class, 'viewById'])
    ->name('builder.page.byId');
