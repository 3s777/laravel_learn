<?php

use App\Http\Controllers\UserController;
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
Route::group(['middleware' => ['auth']], function() {
    Route::get('/', [UserController::class, 'index']);
    Route::get('users/edit_credentials/{id}', [UserController::class, 'edit_credentials'])->name('users.edit_credentials');
    Route::patch('users/update_credentials/{id}', [UserController::class, 'update_credentials'])->name('users.update_credentials');
    Route::get('users/edit_status/{id}', [UserController::class, 'edit_status'])->name('users.edit_status');
    Route::patch('users/update_status/{id}', [UserController::class, 'update_status'])->name('users.update_status');
    Route::get('users/edit_avatar/{id}', [UserController::class, 'edit_avatar'])->name('users.edit_avatar');
    Route::patch('users/update_avatar/{id}', [UserController::class, 'update_avatar'])->name('users.update_avatar');
    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
