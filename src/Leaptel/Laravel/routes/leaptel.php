<?php

use Illuminate\Support\Facades\Route;
use Leaptel\Http\AddressLookup;

Route::get('/leaptel/address', AddressLookup::class)->middleware(['auth', 'verified'])->name('address');
