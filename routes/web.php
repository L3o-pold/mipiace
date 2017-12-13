<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Throw a 404 for all route expect the / for slack command
 */
Route::any('{any}', function () {
    abort(404);
})->where('any', '.*');