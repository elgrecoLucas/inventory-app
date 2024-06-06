<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Products\ListProducts;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/app');
});

Route::get('/livewire', ListProducts::class);
