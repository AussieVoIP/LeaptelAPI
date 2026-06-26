<?php

use Illuminate\Support\Facades\Route;
use Leaptel\Http\Addressify;

// Route::get('/addressify', Addressify::class)->middleware(['auth', 'verified'])->name('addressify');
Route::get('/address', Addressify::class)->name('address');
