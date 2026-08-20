<?php

use App\Http\Controllers\ArticleController;
use App\Livewire\VectorLab;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('vector-lab', VectorLab::class)->name('vector-lab');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
