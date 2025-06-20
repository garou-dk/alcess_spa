<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::view('/{any}', 'welcome')->where('any', '.*');