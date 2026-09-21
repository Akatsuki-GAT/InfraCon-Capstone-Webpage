<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
    //Existed user acc logs in
    public function signin(Request $request) {
    $incomingFields = $request->validate([
        'login' => 'required|string',
        'signinpassword' => 'required|string'
    ]);

    

    if (auth()-> attempt(['email' => $incomingFields['login'], 'password' => $incomingFields['signinpassword'] ])) {
        $request->session()->regenerate();
    /*if (Auth::attempt([
        $loginType => $incomingFields['login'],
        'password' => $incomingFields['signinpassword']
    ])) {
        $request->session()->regenerate();
        //if role is admin 
        if (auth()->user()->role === 'admin') {
        
    }*/

        return redirect()->route('home');
    }  

    
    return back()->with('error', 'Wrong username or password. Please try again.'); 
}

    //User clicks logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    //Register new acc and hashing their password
   /* public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => 'required|min:6|max:255|unique:users,username', //input validations
            'email' => ['required', 'email','max:255', Rule::unique('users', 'email')],
            'full_name'  => ['required', 'regex:/^[A-Za-z\s]+$/', 'max:255'],
            'contact'      => ['nullable', 'regex:/^\+?[0-9]+$/'],
            'password' => ['required','min:8' ,'max:255']
            
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']); //this is the password hash
        $user = User::create($incomingFields);
        Auth::login($user); 
        
        return redirect()->route('index');
       /* return redirect('/');
    }*/
    
}