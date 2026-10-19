<?php

namespace App\Http\Controllers;

use App\Models\construction_projects;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConstructionProjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || in_array($user->role->roleName, ['Foreman', 'Client'], true),
            403
        );

        $query = construction_projects::query()
            ->with(['creator', 'accessibleUsers.role'])
            ->orderByRaw("CASE status WHEN 'active' THEN 1 WHEN 'complete' THEN 2 WHEN 'cancelled' THEN 3 ELSE 4 END")
            ->orderByDesc('startedDate');

        if (! $user->isAdmin()) {
            $query->whereHas('accessibleUsers', fn ($assigned) => $assigned->where('construction_users.UserID', $user->UserID));
        }

        return view('projects.index', [
            'projects' => $query->get(),
            'assignableUsers' => $user->isAdmin() ? $this->assignableUsers() : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProject($request, 'createProject');

        DB::transaction(function () use ($request, $validated): void {
            $project = construction_projects::create([
                ...$validated,
                'createdBy' => $request->user()->UserID,
            ]);

            $project->accessibleUsers()->sync($request->input('accessUserIDs', []));
        });

        return redirect()->route('projects.index')
            ->with('project_management_success', 'The construction project was created.');
    }

    public function update(Request $request, construction_projects $project): RedirectResponse
    {
        $validated = $this->validateProject($request, 'manageProject'.$project->ConProjID);

        DB::transaction(function () use ($request, $project, $validated): void {
            $project->update($validated);
            $project->accessibleUsers()->sync($request->input('accessUserIDs', []));
        });

        return redirect()->route('projects.index')
            ->with('project_management_success', 'The construction project was updated.');
    }

    private function validateProject(Request $request, string $errorBag): array
    {
        $validated = $request->validateWithBag($errorBag, [
            'projectName' => ['required', 'string', 'max:50', "regex:/^[\\pL\\pN\\s.,&()'\\/-]+$/u"],
            'location' => ['required', 'string', 'max:100'],
            'startedDate' => ['required', 'date'],
            'targetEndDate' => ['required', 'date', 'after_or_equal:startedDate'],
            'actualEndDate' => ['nullable', 'date', 'after_or_equal:startedDate', 'required_if:status,complete'],
            'status' => ['required', Rule::in(['active', 'complete', 'cancelled', 'archive'])],
            'accessUserIDs' => ['nullable', 'array'],
            'accessUserIDs.*' => ['integer', 'distinct', Rule::exists('construction_users', 'UserID')],
        ], [
            'projectName.regex' => 'The project name contains an unsupported character.',
            'targetEndDate.after_or_equal' => 'The target end date must be on or after the start date.',
            'actualEndDate.required_if' => 'The actual end date is required when the project is complete.',
            'actualEndDate.after_or_equal' => 'The actual end date must be on or after the start date.',
        ]);

        validator(
            ['accessUserIDs' => $request->input('accessUserIDs', [])],
            ['accessUserIDs' => [function (string $attribute, mixed $value, \Closure $fail): void {
                if (! is_array($value) || $value === []) {
                    return;
                }

                $validCount = User::query()
                    ->whereIn('UserID', $value)
                    ->where('status', 'active')
                    ->whereHas('role', fn ($query) => $query->whereIn('roleName', ['Foreman', 'Client']))
                    ->count();

                if ($validCount !== count(array_unique($value))) {
                    $fail('Only active Foreman and Client accounts may be assigned to a project.');
                }
            }]],
        )->validateWithBag($errorBag);

        unset($validated['accessUserIDs']);

        return $validated;
    }

    private function assignableUsers()
    {
        return User::query()
            ->where('status', 'active')
            ->whereHas('role', fn ($query) => $query->whereIn('roleName', ['Foreman', 'Client']))
            ->with('role')
            ->orderBy('lastName')
            ->orderBy('firstName')
            ->get();
    }
}
