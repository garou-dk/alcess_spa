<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('index');

Route::view('/{any}', 'welcome')->where('any', '.*');
