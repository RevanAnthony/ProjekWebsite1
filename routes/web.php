<?php

use App\Http\Controllers\PageController;

Route::get('/',        [PageController::class,'home'])->name('home');
Route::get('/about',   [PageController::class,'menu'])->name('about');   // <= penting
Route::get('/contact', [PageController::class,'contact'])->name('contact');
Route::post('/contact',[PageController::class,'sendContact'])->name('contact.send');

// Opsional: alias '/menu' juga menuju menu yang sama
Route::get('/menu',    [PageController::class,'menu'])->name('menu');


