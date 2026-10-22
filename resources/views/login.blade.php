<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InfraCon</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            padding: 24px;
            display: grid;
            place-items: center;
            font-family: Arial, sans-serif;
            color: #1f2937;
            background: #f3f4f6;
        }

        .auth-container { width: min(420px, 100%); }

        .auth-card {
            width: 100%;
            padding: 32px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
        }

        .auth-header { margin-bottom: 28px; text-align: center; }
        .auth-header h1 { margin: 0; font-size: 30px; }
        .auth-header p { margin: 9px 0 0; color: #64748b; }
        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        .input-group { position: relative; }

        input {
            width: 100%;
            min-height: 44px;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
            background: #fff;
        }

        input:focus {
            outline: 3px solid rgba(37, 99, 235, .18);
            border-color: #2563eb;
        }

        input:disabled {
            color: #64748b;
            background: #e2e8f0;
            cursor: not-allowed;
        }

        input[type="password"],
        input[type="text"]#password { padding-right: 48px; }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 7px;
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            padding: 0;
            transform: translateY(-50%);
            border: 0;
            border-radius: 6px;
            color: #475569;
            background: transparent;
            cursor: pointer;
        }

        .password-toggle:hover { background: #f1f5f9; }
        .password-toggle svg { width: 21px; height: 21px; }
        .password-toggle svg[hidden] { display: none; }

        .btn {
            width: 100%;
            min-height: 44px;
            margin-top: 4px;
            padding: 11px 18px;
            border: 0;
            border-radius: 8px;
            color: #fff;
            background: #2563eb;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .btn:hover { background: #1d4ed8; }
        .btn:disabled, .password-toggle:disabled { opacity: .6; cursor: not-allowed; }
        .btn:disabled:hover { background: #2563eb; }
        .btn:focus-visible, .password-toggle:focus-visible { outline: 3px solid rgba(37, 99, 235, .3); outline-offset: 2px; }

        .form-message {
            margin-top: 16px;
            padding: 11px 12px;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-message.error { color: #991b1b; background: #fee2e2; }

        @media (max-width: 480px) {
            body { padding: 16px; }
            .auth-card { padding: 24px 20px; }
            .auth-header h1 { font-size: 27px; }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>InfraCon</h1>
                <p>Sign in to your account to continue</p>
            </div>

            <form action="{{ route('signin') }}" id="login-form" class="auth-form" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <input type="email" id="email" name="login" value="{{ old('login') }}" placeholder="name@example.com" autocomplete="email" required autofocus @disabled($lockoutSeconds > 0)>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="signinpassword" autocomplete="current-password" required @disabled($lockoutSeconds > 0)>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')" aria-label="Show password" aria-pressed="false" @disabled($lockoutSeconds > 0)>
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 4.2A10.7 10.7 0 0 1 12 4c6.5 0 10 8 10 8a16 16 0 0 1-2 3M6.6 6.6C3.6 8.6 2 12 2 12s3.5 8 10 8a9.8 9.8 0 0 0 4.1-.9"/></svg>
                            </button>
                    </div>
                </div>

                <button type="submit" id="signin-button" class="btn btn-primary btn-full" @disabled($lockoutSeconds > 0)>
                    <span class="btn-text">Sign In</span>
                </button>
                @if (session('error'))
                <div class="form-message" id="form-message" role="alert">
                    <span>{{ session('error') }}</span>
                    @if ($lockoutSeconds > 0)
                        <span id="lockout-countdown" data-seconds="{{ $lockoutSeconds }}">
                            <strong><span id="lockout-seconds">{{ $lockoutSeconds }}</span> seconds remaining.</strong>
                        </span>
                    @endif
                </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        const messageDiv = document.getElementById('form-message');

        if (messageDiv) {
            messageDiv.classList.add('error');
        }

        const countdown = document.getElementById('lockout-countdown');

        if (countdown) {
            let seconds = Number(countdown.dataset.seconds);
            const secondsText = document.getElementById('lockout-seconds');
            const controls = [
                document.getElementById('email'),
                document.getElementById('password'),
                document.querySelector('.password-toggle'),
                document.getElementById('signin-button'),
            ];

            const timer = window.setInterval(() => {
                seconds -= 1;
                secondsText.textContent = Math.max(0, seconds);

                if (seconds <= 0) {
                    window.clearInterval(timer);
                    controls.forEach((control) => control.disabled = false);
                    countdown.textContent = ' You may try signing in again.';
                    document.getElementById('email').focus();
                }
            }, 1000);
        }
        

        // Password toggle function
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.password-toggle');
            const willShow = input.type === 'password';

            input.type = willShow ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(willShow));
            button.setAttribute('aria-label', willShow ? 'Hide password' : 'Show password');
            button.querySelector('.eye-open').hidden = willShow;
            button.querySelector('.eye-closed').hidden = !willShow;
        }
    </script>
</body>
</html>
