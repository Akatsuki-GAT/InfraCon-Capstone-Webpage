<?php

//use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Main landing page
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('home');
    }

    return redirect()->route('login');
});
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('login'))->name('login');
    Route::post('/signin', [UserController::class, 'signin'])->name('signin');
});

/* Auth once users in and completes either login or register form*/
Route::middleware('auth')->group(function () {
    Route::get('/home', fn () => view('home'))->name('home');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
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
