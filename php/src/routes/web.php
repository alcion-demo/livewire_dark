<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\BookIndex;
use App\Livewire\Dashboard;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    
    // 修正：匿名関数（function）ではなく Dashboard クラスを指定する
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/books', BookIndex::class)->name('books.index');
});