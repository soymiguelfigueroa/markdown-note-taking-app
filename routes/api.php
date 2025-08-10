<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarkdownNoteController;

Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function(){
    Route::get('/user', function (Request $request) {return $request->user();});
    Route::post('/notes', [MarkdownNoteController::class, 'store'])->name('notes.store');
    Route::get('/notes', [MarkdownNoteController::class, 'index'])->name('notes.index');
    Route::get('/notes/{note}', [MarkdownNoteController::class, 'show'])->name('notes.show');
    Route::get('/grammar/{note}', [MarkdownNoteController::class, 'check'])->name('notes.check');
});
