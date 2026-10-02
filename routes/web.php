<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Main landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }

    return redirect()->route('login');
});
Route::middleware('guest')->group(function () { //if users aren't login
    Route::get('/login', fn () => view('login'))->name('login');
    Route::post('/signin', [UserController::class, 'signin'])->name('signin');
});

/* Auth once users in and completes in login*/
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/home', fn () => view('home'))->name('home');
    Route::patch('/account', [UserController::class, 'updateAccount'])->name('account.update');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'active', 'admin'])->prefix('admin')->name('admin.')->group(function () { //is login, currrent status is active, and role is admin
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index'); 
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
});


/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
*/
//require __DIR__.'/auth.php';
