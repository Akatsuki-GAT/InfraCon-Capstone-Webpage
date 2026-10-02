<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'roles' => Role::query()->orderBy('roleName')->get(),
            'users' => User::query()->orderBy('lastName')->orderBy('firstName')->get(),
        ]);
    }
    // account creation
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('createUser', [
            'firstName' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z ]+$/'],
            'lastName' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z ]+$/'],
            'contactNo' => ['required', 'string', 'regex:/^[0-9]{11}$/'],
            'email' => ['required', 'email', 'max:50', Rule::unique('construction_users', 'email')],
            'RoleID' => ['required', 'integer', Rule::exists('user_role', 'RoleID')],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ], $this->messages());

        $user = User::create($validated + ['status' => 'active']);
        $user->notify(new AccountCreatedNotification());

        return redirect()->route('admin.users.index')
            ->with('user_management_success', 'The account was created and the user was notified by email.');
    }
    //updating user's role and status
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validateWithBag('manageUser'.$user->UserID, [
            'RoleID' => ['required', 'integer', Rule::exists('user_role', 'RoleID')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        DB::transaction(function () use ($user, $validated): void {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->UserID);
            $removingActiveAdmin = $lockedUser->isAdmin()
                && $lockedUser->status === 'active'
                && ((int) $validated['RoleID'] !== 1 || $validated['status'] !== 'active');

            if ($removingActiveAdmin) {
                $otherActiveAdmins = User::query()
                    ->where('RoleID', 1)
                    ->where('status', 'active')
                    ->where('UserID', '!=', $lockedUser->UserID)
                    ->exists();

                if (! $otherActiveAdmins) {
                    throw ValidationException::withMessages([
                        'status' => 'Create or activate another Admin account before changing the final active Admin.',
                    ])->errorBag('manageUser'.$lockedUser->UserID);
                }
            }

            $lockedUser->update($validated);
        });

        return redirect()->route('admin.users.index')
            ->with('user_management_success', 'The user role and account status were updated.');
    }

    private function messages(): array
    {
        return [
            'firstName.regex' => 'The first name may contain letters and spaces only.',
            'lastName.regex' => 'The last name may contain letters and spaces only.',
            'contactNo.regex' => 'The contact number must contain exactly 11 digits.',
        ];
    }
}
