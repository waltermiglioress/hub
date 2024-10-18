<?php


use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bi',[\App\Http\Controllers\PowerBI::class,'showTableData'])->name('powerbi.table');

Route::redirect('/','/mng');

//Route::get('/production/export', [\App\Http\Controllers\ProductionController::class,'export'])->name('production.export');
// Rotte per richiedere il reset della password (inserimento email)
