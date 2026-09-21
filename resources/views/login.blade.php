<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - InfraCon</title>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>InfraCon Page</h1>
                <p>Sign in to your account to continue</p>
            </div>

            <form action="/signin" id="login-form" class="auth-form" method="POST">
                @csrf
                <div class="form-group">
                    <label for="signin_email">Email</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="email" name="login" placeholder="JohnDoe@email.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="signin_password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="signinpassword" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <span class="btn-text">Sign In</span>
                    <i class="fas fa-spinner fa-spin btn-loading" style="display: none;"></i>
                </button>
                @if (session('error'))
                <div class="form-message" id="form-message">
                    {{ session ('error') }}
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
        

        // Password toggle function
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.parentElement.querySelector('.password-toggle i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>