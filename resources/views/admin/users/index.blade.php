<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Accounts - InfraCon</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 32px 20px; font-family: Arial, sans-serif; color: #1f2937; background: #f3f4f6; }
        .page { width: min(1180px, 100%); margin: 0 auto; }
        .topbar, .actions, .form-actions { display: flex; align-items: center; gap: 12px; }
        .topbar { justify-content: space-between; margin-bottom: 24px; }
        h1, h2 { margin-top: 0; }
        h1 { margin-bottom: 6px; }
        .subtitle { margin: 0; color: #64748b; }
        .card { margin-bottom: 24px; padding: 24px; border-radius: 12px; background: #fff; box-shadow: 0 8px 24px rgba(15, 23, 42, .07); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field label { display: block; margin-bottom: 7px; font-weight: 600; }
        input, select { width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; background: #fff; }
        input:focus, select:focus { outline: 3px solid rgba(37, 99, 235, .16); border-color: #2563eb; }
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 48px; }
        .password-toggle { position: absolute; top: 50%; right: 7px; width: 36px; height: 36px; transform: translateY(-50%); border: 0; border-radius: 6px; color: #475569; background: transparent; cursor: pointer; }
        .password-toggle:hover { background: #f1f5f9; }
        .password-toggle svg { width: 21px; height: 21px; }
        .button { display: inline-block; padding: 11px 17px; border: 0; border-radius: 8px; color: #fff; background: #2563eb; font: inherit; text-decoration: none; cursor: pointer; }
        .button:hover { background: #1d4ed8; }
        .button-secondary { background: #475569; }
        .button-secondary:hover { background: #334155; }
        .success { margin-bottom: 20px; padding: 13px 15px; border-radius: 8px; color: #166534; background: #dcfce7; }
        .error { margin: 7px 0 0; color: #b91c1c; font-size: 14px; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; }
        .manage-form { display: grid; grid-template-columns: minmax(150px, 1fr) minmax(120px, .7fr) auto; gap: 8px; align-items: start; min-width: 430px; }
        .badge { display: inline-block; padding: 4px 9px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
        .badge-active { color: #166534; background: #dcfce7; }
        .badge-inactive { color: #991b1b; background: #fee2e2; }
        .hint { margin: 8px 0 0; color: #64748b; font-size: 13px; }
        @media (max-width: 720px) {
            .grid { grid-template-columns: 1fr; }
            .topbar { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="topbar">
            <div>
                <h1>Manage User Accounts</h1>
                <p class="subtitle">Create accounts, assign roles, and retain inactive accounts for historical records.</p>
            </div>
            <div class="actions">
                <a class="button button-secondary" href="{{ route('home') }}">Back to Home</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="button button-secondary" type="submit">Log Out</button>
                </form>
            </div>
        </header>

        @if (session('user_management_success'))
            <div class="success" role="status">{{ session('user_management_success') }}</div>
        @endif

        <section class="card">
            <h2>Create User Account</h2>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="grid">
                    <div class="field">
                        <label for="firstName">First Name</label>
                        <input id="firstName" name="firstName" type="text" value="{{ old('firstName') }}" maxlength="50" pattern="[A-Za-z ]+" required>
                        @error('firstName', 'createUser')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="lastName">Last Name</label>
                        <input id="lastName" name="lastName" type="text" value="{{ old('lastName') }}" maxlength="50" pattern="[A-Za-z ]+" required>
                        @error('lastName', 'createUser')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="50" required>
                        @error('email', 'createUser')<p class="error">{{ "This user email is already taken and existed" }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="contactNo">Contact Number</label>
                        <input id="contactNo" name="contactNo" type="tel" value="{{ old('contactNo') }}" maxlength="11" inputmode="numeric" pattern="[0-9]{11}" required>
                        @error('contactNo', 'createUser')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="RoleID">Role</label>
                        <select id="RoleID" name="RoleID" required>
                            <option value="">Select a role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->RoleID }}" @selected((string) old('RoleID') === (string) $role->RoleID)>{{ $role->roleName }}</option>
                            @endforeach
                        </select>
                        @error('RoleID', 'createUser')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div></div>
                    <div class="field">
                        <label for="password">Initial Password</label>
                        <div class="password-wrap">
                            <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-toggle-password="password" aria-label="Show initial password">&#128065;</button>
                        </div>
                        @error('password', 'createUser')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Confirm Initial Password</label>
                        <div class="password-wrap">
                            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-toggle-password="password_confirmation" aria-label="Show password confirmation">&#128065;</button>
                        </div>
                    </div>
                </div>
                <div class="form-actions" style="margin-top: 20px;">
                    <button class="button" type="submit">Create Account</button>
                </div>
                <p class="hint">The new user receives an account creation email. Initial passwords are not included in email messages.</p>
            </form>
        </section>

        <section class="card">
            <h2>Registered Users</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Current status</th>
                            <th>Manage role and status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $managedUser)
                            @php($errorBag = 'manageUser'.$managedUser->UserID)
                            <tr>
                                <td>
                                    <strong>{{ $managedUser->firstName }} {{ $managedUser->lastName }}</strong><br>
                                    {{ $managedUser->email }}
                                </td>
                                <td>{{ $managedUser->contactNo }}</td>
                                <td>
                                    {{ $managedUser->role->roleName }}<br>
                                    <span class="badge badge-{{ $managedUser->status }}">{{ $managedUser->status }}</span>
                                </td>
                                <td>
                                    <form class="manage-form" action="{{ route('admin.users.update', $managedUser) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <select name="RoleID" aria-label="Role for {{ $managedUser->firstName }} {{ $managedUser->lastName }}" required>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->RoleID }}" @selected((string) old('RoleID', $managedUser->RoleID) === (string) $role->RoleID)>{{ $role->roleName }}</option>
                                                @endforeach
                                            </select>
                                            @error('RoleID', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                        </div>
                                        <div>
                                            <select name="status" aria-label="Status for {{ $managedUser->firstName }} {{ $managedUser->lastName }}" required>
                                                <option value="active" @selected(old('status', $managedUser->status) === 'active')>Active</option>
                                                <option value="inactive" @selected(old('status', $managedUser->status) === 'inactive')>Inactive</option>
                                            </select>
                                            @error('status', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                        </div>
                                        <button class="button" type="submit">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No user accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.togglePassword);
                const showing = input.type === 'password';
                input.type = showing ? 'text' : 'password';
                button.setAttribute('aria-label', `${showing ? 'Hide' : 'Show'} password`);
            });
        });
    </script>
</body>
</html>
