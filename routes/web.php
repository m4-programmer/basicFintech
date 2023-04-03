<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/topUp', [App\Http\Controllers\HomeController::class, 'topUp'])->name('topUp');
Route::post('/sendMoney', [App\Http\Controllers\HomeController::class, 'sendMoney'])->name('sendMoney');

// To create route to handle sending and recieving of money

// to include charges when sending money

// to send transaction mail

// to save beneificiary details

// to allow user subscribe to mail service

// to create user account number