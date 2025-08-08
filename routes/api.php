<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarkdownNoteController;

Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/user', function (Request $request) {return $request->user();});
    Route::post('/notes', [MarkdownNoteController::class, 'store'])->name('note.store');
    Route::get('/notes', [MarkdownNoteController::class, 'index'])->name('note.index');
});
