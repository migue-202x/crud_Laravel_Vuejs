<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticuloController;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//para insistirle que son recursos de api se pone apiResource. Entonces no te muestra los metodos 'create' y 'edit'
//cuando se ejecuta php artisan r:l -c
Route::apiResource('/articulos', ArticuloController::class);
