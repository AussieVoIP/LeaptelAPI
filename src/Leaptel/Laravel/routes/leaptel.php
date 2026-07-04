<?php

use Illuminate\Support\Facades\Route;
use Leaptel\Http\AddressLookup;
use Leaptel\Http\EventDetails;

Route::get('/leaptel/address', AddressLookup::class)->middleware(['auth', 'verified'])->name('address');
Route::get('/leaptel/event/{uuid}', EventDetails::class)->middleware(['auth', 'verified'])->name('eventdetails');
