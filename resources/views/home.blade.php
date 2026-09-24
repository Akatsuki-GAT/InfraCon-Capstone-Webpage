<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - InfraCon</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 40px 20px; font-family: Arial, sans-serif; color: #1f2937; background: #f3f4f6; }
        .page { width: min(760px, 100%); margin: 0 auto; }
        .card { padding: 28px; border-radius: 12px; background: #fff; box-shadow: 0 10px 30px rgba(15, 23, 42, .08); }
        h1 { margin: 0 0 10px; font-size: 28px; }
        h2 { margin: 0 0 26px; font-size: 18px; font-weight: 500; color: #4b5563; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; }
        button { font: inherit; }
        .button { border: 0; border-radius: 8px; padding: 11px 18px; color: #fff; background: #2563eb; cursor: pointer; }
        .button:hover { background: #1d4ed8; }
        .button-secondary { background: #4b5563; }
        .button-secondary:hover { background: #374151; }
        .button-plain { color: #374151; background: #e5e7eb; }
        .button-plain:hover { background: #d1d5db; }
        .success { margin-bottom: 18px; padding: 12px 14px; border-radius: 8px; color: #166534; background: #dcfce7; }
        dialog { width: min(500px, calc(100% - 32px)); padding: 0; border: 0; border-radius: 12px; box-shadow: 0 24px 60px rgba(0, 0, 0, .25); }
        dialog::backdrop { background: rgba(15, 23, 42, .55); }
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; }
        .modal-header h3 { margin: 0; font-size: 21px; }
        .close-button { width: 36px; height: 36px; border: 0; border-radius: 50%; font-size: 25px; line-height: 1; color: #4b5563; background: transparent; cursor: pointer; }
        .close-button:hover { background: #f3f4f6; }
        .modal-body { padding: 24px; }
        .field { margin-bottom: 18px; }
        label { display: block; margin-bottom: 7px; font-weight: 600; }
        input { width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; }
        input:focus { outline: 3px solid rgba(37, 99, 235, .18); border-color: #2563eb; }
        input[readonly] { color: #4b5563; background: #f3f4f6; }
        .password-field { position: relative; }
        .password-field input { padding-right: 48px; }
        .password-toggle { position: absolute; top: 50%; right: 8px; display: grid; place-items: center; width: 36px; height: 36px; padding: 0; transform: translateY(-50%); border: 0; border-radius: 6px; color: #4b5563; background: transparent; cursor: pointer; }
        .password-toggle:hover { background: #f3f4f6; }
        .password-toggle svg { width: 21px; height: 21px; }
        .error { margin: 7px 0 0; color: #b91c1c; font-size: 14px; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; }
    </style>
</head>
<body>
    <main class="page">
        @if (session('account_updated'))
            <div class="success" role="status">{{ session('account_updated') }}</div>
        @endif

        <!--Current Profile Info-->
        <section class="card">
            <h1>InfraCon User: {{ Auth::user()->firstName }} {{ Auth::user()->lastName }}</h1>
            <h2>InfraCon User Role: {{ Auth::user()->role->roleName }} <br>
            InfraCon User Email: {{ Auth::user()->email }}<br>
            InfraCon Contact No.: {{ Auth::user()->contactNo }}</h2>

            <div class="actions">
                <button type="button" class="button" id="open-account-dialog">Update Account</button>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="button button-secondary">Log Out</button>
                </form>
            </div>
        </section>
    </main>

    <!--Updating Profile-->
    <dialog id="account-dialog" aria-labelledby="account-dialog-title">
        <div class="modal-header">
            <h3 id="account-dialog-title">Update Account</h3>
            <button type="button" class="close-button" data-close-dialog aria-label="Close">&times;</button>
        </div>

        <form action="{{ route('account.update') }}" method="POST" class="modal-body">
            @csrf
            @method('PATCH')
            <!--First Name-->
            <div class="field">
                <label for="current-firstName">Current First Name</label>
                <input id="current-firstName" type="text" value="{{ Auth::user()->firstName }}" readonly>
            </div>
            <!--Update First Name-->
            <div class="field">
                <label for="firstName">Enter New First Name</label>
                <input id="firstName" name="firstName" type="text" value="{{ old('firstName') }}" maxlength="50" autocomplete="firstName" >
                @error('firstName', 'updateAccount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <!--Last Name-->
            <div class="field">
                <label for="current-lastName">Current Last Name</label>
                <input id="current-lastName" type="text" value="{{ Auth::user()->lastName}}" readonly>
            </div>
            <!--Update Last Name-->
            <div class="field">
                <label for="lastName">Enter Last Name</label>
                <input id="lastName" name="lastName" type="text" value="{{ old('lastName') }}" maxlength="50" autocomplete="lastName" >
                @error('lastName', 'updateAccount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <!--Contact No.-->
            <div class="field">
                <label for="current-contactNo">Current Contact No.</label>
                <input id="current-contactNo" type="number" value="{{ Auth::user()->contactNo }}" readonly>
            </div>
            <!--Update Contact No.-->
            <div class="field">
                <label for="contactNo">Enter New Contact No.</label>
                <input id="contactNo" name="contactNo" type="tel" value="{{ old('contactNo') }}" maxlength="11" inputmode="numeric" pattern="[0-9]{11}" autocomplete="tel" >
                @error('contactNo', 'updateAccount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <!--Email-->
            <div class="field">
                <label for="current-email">Current Email</label>
                <input id="current-email" type="email" value="{{ Auth::user()->email }}" readonly>
            </div>
            <!--Update Email-->
            <div class="field">
                <label for="email">Enter New Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="50" autocomplete="email" >
                @error('email', 'updateAccount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <!--Password(old)-->
            <div class="field">
                <label for="current-password">Old Password</label>
                <div class="password-field">
                    <input id="current-password" name="current_password" type="password" autocomplete="current-password" required>
                    <button type="button" class="password-toggle" data-password-toggle="current-password" aria-label="Show old password" aria-pressed="false">
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 4.2A10.7 10.7 0 0 1 12 4c6.5 0 10 8 10 8a16 16 0 0 1-2 3M6.6 6.6C3.6 8.6 2 12 2 12s3.5 8 10 8a9.8 9.8 0 0 0 4.1-.9"/></svg>
                    </button>
                </div>
                @error('current_password', 'updateAccount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <!--Password(new/update)-->
            <div class="field">
                <label for="new-password">New Password</label>
                <div class="password-field">
                    <input id="new-password" name="password" type="password" minlength="8" autocomplete="new-password" >
                    <button type="button" class="password-toggle" data-password-toggle="new-password" aria-label="Show new password" aria-pressed="false">
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" hidden><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 4.2A10.7 10.7 0 0 1 12 4c6.5 0 10 8 10 8a16 16 0 0 1-2 3M6.6 6.6C3.6 8.6 2 12 2 12s3.5 8 10 8a9.8 9.8 0 0 0 4.1-.9"/></svg>
                    </button>
                </div>
                @error('password', 'updateAccount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="modal-actions">
                <button type="button" class="button button-plain" data-close-dialog>Cancel</button>
                <button type="submit" class="button">Save Changes</button>
            </div>
        </form>
    </dialog>

    <script>
        const accountDialog = document.getElementById('account-dialog');

        document.getElementById('open-account-dialog').addEventListener('click', () => accountDialog.showModal());

        document.querySelectorAll('[data-close-dialog]').forEach((button) => {
            button.addEventListener('click', () => accountDialog.close());
        });

        accountDialog.addEventListener('click', (event) => {
            if (event.target === accountDialog) accountDialog.close();
        });

        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const willShow = input.type === 'password';

                input.type = willShow ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(willShow));
                button.setAttribute('aria-label', `${willShow ? 'Hide' : 'Show'} ${input.id === 'current-password' ? 'old' : 'new'} password`);
                button.querySelector('.eye-open').hidden = willShow;
                button.querySelector('.eye-closed').hidden = !willShow;
            });
        });

        @if ($errors->updateAccount->any())
            accountDialog.showModal();
        @endif
    </script>
</body>
</html>
