<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

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


Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
    });

    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/users', [TaskController::class, 'addUser'])->name('tasks.users.add');
    Route::delete('tasks/{task}/users', [TaskController::class, 'removeUser'])->name('tasks.users.remove');
    Route::patch('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
});
