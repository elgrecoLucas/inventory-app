<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Products\ListProducts;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/app');
});

Route::get('/cancel-order/{order}', [OrderController::class, 'cancel'])->name('order.cancel');

Route::get('/livewire', ListProducts::class);
