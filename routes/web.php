<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoteBookController;

Route::get('/', function () {
    return view('welcome');
    // $users = DB::select('select * from users');
    // dd($users);
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// All the auth-related routes can be found here
require __DIR__.'/auth.php';


/**
 * Note Routes 
 */

/**
 * With middleware auth only users who have logged in can access the following links
 * Register a single resource route that points to the resource controller w/ auth  for users who sign in
 */

Route::resource('notes', NoteController::class)->middleware('auth');
Route::resource('notebooks', NoteBookController::class)->middleware('auth');