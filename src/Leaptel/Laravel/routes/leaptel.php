<?php

use Illuminate\Support\Facades\Route;
use Leaptel\Http\Addressify;

Route::get('/leaptel/address', Addressify::class)->middleware(['auth', 'verified'])->name('address');
