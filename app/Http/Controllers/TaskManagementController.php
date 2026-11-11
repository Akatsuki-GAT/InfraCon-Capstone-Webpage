<?php

namespace App\Http\Controllers;

use App\Models\construction_progress;
use App\Models\construction_projects;
use App\Models\construction_tasks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TaskManagementController extends Controller
{
    public function index(Request $request, construction_projects $project): View
    {
        $this->authorizeProjectManager($request->user(), $project);

        $project->load([
            'progressRecords' => fn ($query) => $query
                ->with(['creator', 'updater', 'tasks.creator', 'tasks.updater'])
                ->orderBy('startDate')
                ->orderBy('ConProgressID'),
        ]);

        foreach ($project->progressRecords as $progress) {
            $progress->setRelation(
                'tasks',
                $progress->tasks->sortBy([
                    ['startDate', 'asc'],
                    ['TaskID', 'asc'],
                ])->values()
            );
        }

        return view('tasks.index', compact('project'));
    }

    public function storeProgress(Request $request, construction_projects $project): RedirectResponse /*Create and store progress*/
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->ensureActiveProject($project, 'createProgress');

        $validated = $this->validateProgress($request, $project, 'createProgress');

        $project->progressRecords()->create([
            ...$validated,
            'createdBy' => $request->user()->UserID,
            'updatedBy' => $request->user()->UserID,
        ]);

        return redirect()->route('projects.tasks.index', $project)
            ->with('task_management_success', 'The construction progress record was created.');
    }

    public function updateProgress( /*Update existing progress*/
        Request $request,
        construction_progress $progress
    ): RedirectResponse {
        abort_unless($request->user()->isAdmin(), 403);

        $project = $progress->project;
        $this->ensureActiveProject($project, 'manageProgress'.$progress->ConProgressID);
        $validated = $this->validateProgress(
            $request,
            $project,
            'manageProgress'.$progress->ConProgressID,
            $progress
        );

        $outsideUpdatedPeriod = $progress->tasks()
            ->where(function ($query) use ($validated): void {
                $query->whereDate('startDate', '<', $validated['startDate'])
                    ->orWhereDate('startDate', '>', $validated['finishDate']);
            })
            ->exists();

        if ($outsideUpdatedPeriod) {
            throw ValidationException::withMessages([
                'startDate' => 'The updated progress period must still include every existing task start date.',
            ])->errorBag('manageProgress'.$progress->ConProgressID);
        }

        if ($validated['status'] === 'complete' && $progress->tasks()->where('status', 'active')->exists()) {
            throw ValidationException::withMessages([
                'status' => 'Complete or archive all active tasks before completing this progress record.',
            ])->errorBag('manageProgress'.$progress->ConProgressID);
        }

        $progress->update([
            ...$validated,
            'updatedBy' => $request->user()->UserID,
        ]);

        return redirect()->route('projects.tasks.index', $project)
            ->with('task_management_success', 'The construction progress record was updated.');
    }

    public function storeTask( /*Create and store task*/
        Request $request,
        construction_projects $project,
        construction_progress $progress
    ): RedirectResponse {
        $this->authorizeProjectManager($request->user(), $project);
        abort_unless($progress->ConProjID === $project->ConProjID, 404);
        $this->ensureActiveProject($project, 'createTask'.$progress->ConProgressID);

        if ($progress->status !== 'ongoing') {
            throw ValidationException::withMessages([
                'taskName' => 'Tasks can only be created under an ongoing progress record.',
            ])->errorBag('createTask'.$progress->ConProgressID);
        }

        $validated = $this->validateTask(
            $request,
            $progress,
            'createTask'.$progress->ConProgressID
        );

        $progress->tasks()->create([
            ...$validated,
            'createdBy' => $request->user()->UserID,
            'updatedBy' => $request->user()->UserID,
        ]);

        return redirect()->route('projects.tasks.index', $project)
            ->with('task_management_success', 'The construction task was created.');
    }

    public function updateTask(Request $request, construction_tasks $task): RedirectResponse /*Update existing task*/
    {
        $task->loadMissing('progress.project');
        $progress = $task->progress;
        $project = $progress->project;
        $this->authorizeProjectManager($request->user(), $project);
        $this->ensureActiveProject($project, 'manageTask'.$task->TaskID);

        $validated = $this->validateTask(
            $request,
            $progress,
            'manageTask'.$task->TaskID,
            $task
        );

        $task->update([
            ...$validated,
            'updatedBy' => $request->user()->UserID,
        ]);

        return redirect()->route('projects.tasks.index', $project)
            ->with('task_management_success', 'The construction task was updated.');
    }

    //FIELD VALIDATION
    private function validateProgress(
        Request $request,
        construction_projects $project,
        string $errorBag,
        ?construction_progress $progress = null
    ): array {
        $uniqueName = Rule::unique('construction_progress', 'progressName')
            ->where(fn ($query) => $query->where('ConProjID', $project->ConProjID));

        if ($progress) {
            $uniqueName->ignore($progress->ConProgressID, 'ConProgressID');
        }

        $validated = $request->validateWithBag($errorBag, [
            'progressName' => ['required', 'string', 'max:255', $uniqueName],
            'progressDesc' => ['nullable', 'string', 'max:255'],
            'startDate' => ['required', 'date'],
            'finishDate' => ['required', 'date', 'after_or_equal:startDate'],
            'status' => ['required', Rule::in(['ongoing', 'complete', 'archive'])],
        ], [
            'progressName.unique' => 'This project already has a progress record with that name.',
            'finishDate.after_or_equal' => 'The finish date must be on or after the start date.',
        ]);

        $this->ensureDateWithinRange(
            $validated['startDate'],
            $project->actualStartDate,
            $project->targetEndDate,
            'startDate',
            'The progress start date must be within the project planned period.',
            $errorBag
        );
        $this->ensureDateWithinRange(
            $validated['finishDate'],
            $project->actualStartDate,
            $project->targetEndDate,
            'finishDate',
            'The progress finish date must be within the project planned period.',
            $errorBag
        );

        return $validated;
    }

    private function validateTask(
        Request $request,
        construction_progress $progress,
        string $errorBag,
        ?construction_tasks $task = null
    ): array {
        $uniqueName = Rule::unique('construction_tasks', 'taskName')
            ->where(fn ($query) => $query->where('ConProgressID', $progress->ConProgressID));

        if ($task) {
            $uniqueName->ignore($task->TaskID, 'TaskID');
        }

        $validated = $request->validateWithBag($errorBag, [
            'taskName' => ['required', 'string', 'max:55', $uniqueName],
            'taskDesc' => ['nullable', 'string', 'max:255'],
            'startDate' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'complete', 'archive'])],
        ], [
            'taskName.unique' => 'This progress record already has a task with that name.',
        ]);

        $this->ensureDateWithinRange(
            $validated['startDate'],
            $progress->startDate,
            $progress->finishDate,
            'startDate',
            'The task start date must be within its construction progress period.',
            $errorBag
        );

        return $validated;
    }

    private function ensureDateWithinRange(
        string $value,
        mixed $minimum,
        mixed $maximum,
        string $field,
        string $message,
        string $errorBag
    ): void {
        $date = Carbon::parse($value)->startOfDay();
        $minimumDate = Carbon::parse($minimum)->startOfDay();
        $maximumDate = Carbon::parse($maximum)->startOfDay();

        if ($date->lt($minimumDate) || $date->gt($maximumDate)) {
            throw ValidationException::withMessages([
                $field => $message,
            ])->errorBag($errorBag);
        }
    }

    private function authorizeProjectManager(User $user, construction_projects $project): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $isAssignedForeman = $user->role->roleName === 'Foreman'
            && $project->accessibleUsers()
                ->where('construction_users.UserID', $user->UserID)
                ->exists();

        abort_unless($isAssignedForeman, 403);
    }

    private function ensureActiveProject(construction_projects $project, string $errorBag): void
    {
        if ($project->status !== 'active') {
            throw ValidationException::withMessages([
                'project' => 'New progress records and tasks can only be added to an active project.',
            ])->errorBag($errorBag);
        }
    }
}
