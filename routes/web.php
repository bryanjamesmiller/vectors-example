<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use App\Livewire\RagChat;
use App\Livewire\TuitionBillPayment;
use App\Livewire\VectorLab;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('vector-lab', VectorLab::class)->name('vector-lab');
Route::get('rag', RagChat::class)->name('rag');
Route::get('payments', TuitionBillPayment::class)->name('payments');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
