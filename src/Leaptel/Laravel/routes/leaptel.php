<?php

use Illuminate\Support\Facades\Route;
use Leaptel\Http\AddressLookup;
use Leaptel\Http\EventDetails;
use Leaptel\Http\SvcAssuranceDetails;
use Leaptel\Http\SvcOrderDetails;

Route::get('/leaptel/address', AddressLookup::class)->middleware(['auth', 'verified'])->name('address');
Route::get('/leaptel/event/{uuid}', EventDetails::class)->middleware(['auth', 'verified'])->name('eventdetails');
Route::get('/leaptel/sa/{uuid}', SvcAssuranceDetails::class)->middleware(['auth', 'verified'])->name('sadetails');
Route::get('/leaptel/so/{orderhash}', SvcOrderDetails::class)->middleware(['auth', 'verified'])->name('sodetails');
