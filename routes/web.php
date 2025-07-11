<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpdatejsonController;

Route::get('/', [UpdatejsonController::class, 'index']);
Route::post('/update-json', [UpdatejsonController::class, 'updateJson'])->name('update-json');
Route::post('/upload-folder', [UpdatejsonController::class, 'uploadFolder'])->name('upload-folder');
Route::get('/downloadFolder', [UpdatejsonController::class, 'downloadFolder'])->name('downloadFolder');