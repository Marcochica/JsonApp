<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpdatejsonController;

Route::get('/', [UpdatejsonController::class, 'index']);
Route::post('/update-json', [UpdatejsonController::class, 'updateJson'])->name('update-json');

