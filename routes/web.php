<?php

//namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

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

Route::get('/inovicePDF/{id}', [App\Http\Controllers\SalesHotelController::class,'generarPDF']);
Route::get('/generarTicketPDF/{id}', [App\Http\Controllers\SalesController::class,'generarPDF']);
Route::get('/generarTicketCajaPDF/{id}', [App\Http\Controllers\CashController::class,'generarPDF']);
Route::get('/generarTicketCuentaPDF/{id}', [App\Http\Controllers\OrdenController::class,'generarPDF']);

Route::get('/exportarReporteComp/{id}', [App\Http\Controllers\ExportsController::class,'exportarReporteComp']);
Route::get('/exportarReporteRanking/{id}', [App\Http\Controllers\ExportsController::class,'exportarReporteRanking']);

Route::get('/{any}', function () {
    return view('welcome');
})->where('any','.*');


//UPLOAD IMAGES
Route::post('files/edit-file',  [App\Http\Controllers\FileEntriesController::class,'editFile']);
Route::post('files/upload-file',  [App\Http\Controllers\FileEntriesController::class,'uploadFile']);
Route::post('files/configuration',  [App\Http\Controllers\FileEntriesController::class,'configurationFile']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
