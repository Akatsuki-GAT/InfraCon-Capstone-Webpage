<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;

    private const LOGIN_DECAY_SECONDS = 60;

    public function login(Request $request): View
    {
        $lockoutUntil = (int) $request->session()->get('login_lockout_until', 0);
        $lockoutSeconds = max(0, $lockoutUntil - now()->timestamp);

        if ($lockoutSeconds === 0) {
            $request->session()->forget('login_lockout_until');
        }

        return view('login', compact('lockoutSeconds'));
    }
    
    //Existed user acc logs in
    public function signin(Request $request)
    {
        $incomingFields = $request->validate([
            'login' => ['required', 'email'],
            'signinpassword' => ['required', 'string'],
        ]);

        $email = Str::lower(trim($incomingFields['login']));

        if (! User::query()->where('email', $email)->exists()) {
            return back()->withInput($request->only('login'))->with(
                'error',
                'No account is registered with this email address.'
            );
        }

        $throttleKey = $this->loginThrottleKey($email, $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            return $this->loginLockoutResponse($request, $throttleKey);
        }

        if (Auth::attempt([
            'email' => $email,
            'password' => $incomingFields['signinpassword'],
        ])) {
            RateLimiter::clear($throttleKey);
            $request->session()->forget('login_lockout_until');

            if (Auth::user()->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput($request->only('login'))
                    ->with('error', 'This account is inactive. Contact an administrator.');
            }

            $request->session()->regenerate();

            return redirect()->route('home');
        }

        RateLimiter::hit($throttleKey, self::LOGIN_DECAY_SECONDS);
        $attempts = RateLimiter::attempts($throttleKey);

        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            return $this->loginLockoutResponse($request, $throttleKey);
        }

        $remainingAttempts = self::MAX_LOGIN_ATTEMPTS - $attempts;

        $attemptMessage = $remainingAttempts === 1
            ? '1 last login attempt remaining.'
            : $remainingAttempts.' login attempts remaining.';

        return back()->withInput($request->only('login'))->with(
            'error',
            'Wrong email or password. '.$attemptMessage
        );
    }

    private function loginThrottleKey(string $email, ?string $ip): string
    {
        return Str::transliterate(Str::lower(trim($email))).'|'.$ip;
    }

    private function loginLockoutResponse(Request $request, string $throttleKey)
    {
        $seconds = max(1, RateLimiter::availableIn($throttleKey));
        $request->session()->put('login_lockout_until', now()->timestamp + $seconds);

        return back()->withInput($request->only('login'))
            ->with('error', 'Too many failed login attempts. Try again when the cooldown ends.');
    }

    //User clicks logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Update the email and password of the currently authenticated user.
    public function updateAccount(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validateWithBag('updateAccount', [
            'firstName' => ['nullable', 'string', 'max:50','regex:/^[a-zA-Z\s]+$/u'],
            'lastName' => ['nullable', 'string', 'max:50','regex:/^[a-zA-Z\s]+$/u'],
            'contactNo' => ['nullable', 'string', 'regex:/^[0-9]{11}$/'],
            'email' => [
                'nullable',
                'email',
                'max:50',
                Rule::unique('construction_users', 'email')->ignore($user->UserID, 'UserID'),
            ],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
        ], [
            'current_password.current_password' => 'The old password is incorrect.',
            'contactNo.regex' => 'The contact number must contain exactly 11 digits.',
        ]);

        $updates = [];

        foreach (['firstName', 'lastName', 'contactNo', 'email', 'password'] as $field) {
            if ($request->filled($field)) {
                $updates[$field] = $validated[$field];
            }
        }

        if ($updates !== []) {
            $user->update($updates);
        }

        return redirect()->route('home')->with('account_updated', 'Your account was updated successfully.');
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
