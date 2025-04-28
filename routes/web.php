<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Storage;

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

Route::get('/inventory/form', [InventoryController::class, 'showForm'])->name('inventory.form');
Route::post('/inventory/process', [InventoryController::class, 'process'])->name('inventory.process');

Route::get('/inventory/download/{filename}', function ($filename) {
    $path = 'temp/' . $filename;

    if (!Storage::exists($path)) {
        abort(404);
    }

    return Storage::download($path, 'updated_inventory.csv');
})->name('inventory.download');
