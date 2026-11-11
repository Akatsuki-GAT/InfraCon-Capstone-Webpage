<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks - InfraCon</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 32px 20px; font-family: Arial, sans-serif; color: #1f2937; background: #f3f4f6; }
        .page { width: min(1180px, 100%); margin: 0 auto; }
        .topbar, .actions, .form-actions, .heading-row { display: flex; align-items: center; gap: 12px; }
        .topbar, .heading-row { justify-content: space-between; }
        .topbar { margin-bottom: 24px; }
        h1, h2, h3, h4 { margin-top: 0; }
        h1 { margin-bottom: 6px; }
        .subtitle, .muted, .hint { color: #64748b; }
        .subtitle { margin: 0; }
        .card, .progress-card, .task-card { border-radius: 12px; background: #fff; }
        .card { margin-bottom: 24px; padding: 24px; box-shadow: 0 8px 24px rgba(15, 23, 42, .07); }
        .progress-list { display: grid; gap: 22px; }
        .progress-card { padding: 22px; border: 1px solid #cbd5e1; }
        .progress-card h3 { margin-bottom: 5px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .field label { display: block; margin-bottom: 7px; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; background: #fff; }
        input, select { min-height: 44px; }
        textarea { min-height: 88px; resize: vertical; }
        input:focus, textarea:focus, select:focus { outline: 3px solid rgba(37, 99, 235, .16); border-color: #2563eb; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 11px 17px; border: 0; border-radius: 8px; color: #fff; background: #2563eb; font: inherit; text-decoration: none; cursor: pointer; }
        .button:hover { background: #1d4ed8; }
        .button-secondary { background: #475569; }
        .button-secondary:hover { background: #334155; }
        .success { margin-bottom: 20px; padding: 13px 15px; border-radius: 8px; color: #166534; background: #dcfce7; }
        .error { margin: 7px 0 0; color: #b91c1c; font-size: 14px; }
        .hint { margin: 7px 0 0; font-size: 13px; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
        .badge-ongoing, .badge-active { color: #166534; background: #dcfce7; }
        .badge-complete { color: #1e40af; background: #dbeafe; }
        .badge-archive { color: #475569; background: #e2e8f0; }
        .period { margin: 0 0 14px; color: #475569; }
        .description { margin: 0 0 14px; line-height: 1.55; }
        details { margin-top: 16px; }
        summary { color: #1d4ed8; font-weight: 700; cursor: pointer; }
        .details-form { margin-top: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
        .task-section { margin-top: 22px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .task-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; margin-top: 16px; }
        .task-card { padding: 16px; border: 1px solid #e2e8f0; background: #f8fafc; }
        .task-card .heading-row { align-items: flex-start; }
        .task-card h4 { margin-bottom: 5px; }
        .empty { margin: 0; color: #64748b; }
        @media (max-width: 820px) {
            body { padding: 24px 16px; }
            .task-list { grid-template-columns: 1fr; }
        }
        @media (max-width: 680px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .actions { width: 100%; flex-direction: column; align-items: stretch; }
            .actions > *, .actions form, .actions .button { width: 100%; }
            .grid { grid-template-columns: 1fr; }
            .heading-row { align-items: flex-start; flex-direction: column; }
        }
        @media (max-width: 480px) {
            body { padding: 16px 12px; }
            h1 { font-size: 26px; }
            h2 { font-size: 21px; }
            .card, .progress-card { padding: 18px; }
            .form-actions, .form-actions .button { width: 100%; }
        }
    </style>
</head>
<body>
<main class="page">
    <header class="topbar">
        <div>
            <h1>Manage Tasks</h1>
            <p class="subtitle">{{ $project->projectName }} &middot; {{ $project->location }}</p>
        </div>
        <div class="actions">
            <a class="button button-secondary" href="{{ route('projects.index') }}">Back to Projects</a>
            <a class="button button-secondary" href="{{ route('home') }}">Home</a>
        </div>
    </header>

    @if (session('task_management_success'))
        <div class="success" role="status">{{ session('task_management_success') }}</div>
    @endif

    <section class="card">
        <div class="heading-row">
            <div>
                <h2>Project Schedule</h2>
                <p class="subtitle">
                    {{ $project->actualStartDate->format('M j, Y') }} to {{ $project->targetEndDate->format('M j, Y') }}
                </p>
            </div>
            <span class="badge badge-{{ $project->status === 'active' ? 'active' : ($project->status === 'complete' ? 'complete' : 'archive') }}">{{ $project->status }}</span>
        </div>
    </section>

    @if (Auth::user()->isAdmin() && $project->status === 'active')
        <section class="card">
            <h2>Create Construction Progress</h2>
            <p class="subtitle">Create a milestone or major project stage before adding its tasks.</p>
            <form action="{{ route('admin.projects.progress.store', $project) }}" method="POST" style="margin-top: 20px;">
                @csrf
                <div class="grid">
                    <div class="field">
                        <label for="progressName">Progress Name</label>
                        <input id="progressName" name="progressName" value="{{ old('progressName') }}" maxlength="255" required>
                        @error('progressName', 'createProgress')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="progressStatus">Status</label>
                        <select id="progressStatus" name="status" required>
                            @foreach (['ongoing' => 'Ongoing', 'complete' => 'Complete', 'archive' => 'Archive'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'ongoing') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status', 'createProgress')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="progressStartDate">Start Date</label>
                        <input id="progressStartDate" name="startDate" type="date" value="{{ old('startDate') }}" min="{{ $project->actualStartDate->format('Y-m-d') }}" max="{{ $project->targetEndDate->format('Y-m-d') }}" required>
                        @error('startDate', 'createProgress')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="progressFinishDate">Finish Date</label>
                        <input id="progressFinishDate" name="finishDate" type="date" value="{{ old('finishDate') }}" min="{{ $project->actualStartDate->format('Y-m-d') }}" max="{{ $project->targetEndDate->format('Y-m-d') }}" required>
                        @error('finishDate', 'createProgress')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field" style="grid-column: 1 / -1;">
                        <label for="progressDesc">Description</label>
                        <textarea id="progressDesc" name="progressDesc" maxlength="255">{{ old('progressDesc') }}</textarea>
                        @error('progressDesc', 'createProgress')<p class="error">{{ $message }}</p>@enderror
                    </div>
                </div>
                @error('project', 'createProgress')<p class="error">{{ $message }}</p>@enderror
                <div class="form-actions" style="margin-top: 18px;">
                    <button class="button" type="submit">Create Progress</button>
                </div>
            </form>
        </section>
    @endif

    <section class="card">
        <h2>Progress and Tasks</h2>
        <div class="progress-list">
            @forelse ($project->progressRecords as $progress)
                @php
                    $progressErrorBag = 'manageProgress'.$progress->ConProgressID;
                    $progressHasErrors = $errors->getBag($progressErrorBag)->any();
                    $createTaskErrorBag = 'createTask'.$progress->ConProgressID;
                    $createTaskHasErrors = $errors->getBag($createTaskErrorBag)->any();
                @endphp
                <article class="progress-card">
                    <div class="heading-row">
                        <div>
                            <h3>{{ $progress->progressName }}</h3>
                            <p class="period">{{ $progress->startDate->format('M j, Y') }} to {{ $progress->finishDate->format('M j, Y') }}</p>
                        </div>
                        <span class="badge badge-{{ $progress->status }}">{{ $progress->status }}</span>
                    </div>

                    @if ($progress->progressDesc)
                        <p class="description">{{ $progress->progressDesc }}</p>
                    @endif
                    <p class="hint">Created by {{ $progress->creator->firstName }} {{ $progress->creator->lastName }}</p>

                    @if (Auth::user()->isAdmin() && $project->status === 'active')
                        <details @if($progressHasErrors) open @endif>
                            <summary>Edit progress</summary>
                            <form class="details-form" action="{{ route('admin.progress.update', $progress) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="grid">
                                    <div class="field">
                                        <label for="progressName-{{ $progress->ConProgressID }}">Progress Name</label>
                                        <input id="progressName-{{ $progress->ConProgressID }}" name="progressName" value="{{ $progressHasErrors ? old('progressName') : $progress->progressName }}" maxlength="255" required>
                                        @error('progressName', $progressErrorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="progressStatus-{{ $progress->ConProgressID }}">Status</label>
                                        <select id="progressStatus-{{ $progress->ConProgressID }}" name="status" required>
                                            @foreach (['ongoing' => 'Ongoing', 'complete' => 'Complete', 'archive' => 'Archive'] as $value => $label)
                                                <option value="{{ $value }}" @selected(($progressHasErrors ? old('status') : $progress->status) === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('status', $progressErrorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="progressStart-{{ $progress->ConProgressID }}">Start Date</label>
                                        <input id="progressStart-{{ $progress->ConProgressID }}" name="startDate" type="date" value="{{ $progressHasErrors ? old('startDate') : $progress->startDate->format('Y-m-d') }}" required>
                                        @error('startDate', $progressErrorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="progressFinish-{{ $progress->ConProgressID }}">Finish Date</label>
                                        <input id="progressFinish-{{ $progress->ConProgressID }}" name="finishDate" type="date" value="{{ $progressHasErrors ? old('finishDate') : $progress->finishDate->format('Y-m-d') }}" required>
                                        @error('finishDate', $progressErrorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field" style="grid-column: 1 / -1;">
                                        <label for="progressDesc-{{ $progress->ConProgressID }}">Description</label>
                                        <textarea id="progressDesc-{{ $progress->ConProgressID }}" name="progressDesc" maxlength="255">{{ $progressHasErrors ? old('progressDesc') : $progress->progressDesc }}</textarea>
                                        @error('progressDesc', $progressErrorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                                <div class="form-actions" style="margin-top: 16px;">
                                    <button class="button" type="submit">Save Progress</button>
                                </div>
                            </form>
                        </details>
                    @endif

                    <section class="task-section">
                        <h3>Tasks</h3>

                        @if ($project->status === 'active' && $progress->status === 'ongoing')
                            <details @if($createTaskHasErrors) open @endif>
                                <summary>Create task</summary>
                                <form class="details-form" action="{{ route('projects.progress.tasks.store', [$project, $progress]) }}" method="POST">
                                    @csrf
                                    <div class="grid">
                                        <div class="field">
                                            <label for="taskName-{{ $progress->ConProgressID }}">Task Name</label>
                                            <input id="taskName-{{ $progress->ConProgressID }}" name="taskName" value="{{ $createTaskHasErrors ? old('taskName') : '' }}" maxlength="55" required>
                                            @error('taskName', $createTaskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="field">
                                            <label for="taskStart-{{ $progress->ConProgressID }}">Start Date</label>
                                            <input id="taskStart-{{ $progress->ConProgressID }}" name="startDate" type="date" value="{{ $createTaskHasErrors ? old('startDate') : '' }}" min="{{ $progress->startDate->format('Y-m-d') }}" max="{{ $progress->finishDate->format('Y-m-d') }}" required>
                                            @error('startDate', $createTaskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="field">
                                            <label for="taskStatus-{{ $progress->ConProgressID }}">Status</label>
                                            <select id="taskStatus-{{ $progress->ConProgressID }}" name="status" required>
                                                @foreach (['active' => 'Active', 'complete' => 'Complete', 'archive' => 'Archive'] as $value => $label)
                                                    <option value="{{ $value }}" @selected(($createTaskHasErrors ? old('status', 'active') : 'active') === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('status', $createTaskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                        </div>
                                        <div class="field">
                                            <label for="taskDesc-{{ $progress->ConProgressID }}">Description</label>
                                            <textarea id="taskDesc-{{ $progress->ConProgressID }}" name="taskDesc" maxlength="255">{{ $createTaskHasErrors ? old('taskDesc') : '' }}</textarea>
                                            @error('taskDesc', $createTaskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                    @error('project', $createTaskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                    <div class="form-actions" style="margin-top: 16px;">
                                        <button class="button" type="submit">Create Task</button>
                                    </div>
                                </form>
                            </details>
                        @endif

                        <div class="task-list">
                            @forelse ($progress->tasks as $task)
                                @php
                                    $taskErrorBag = 'manageTask'.$task->TaskID;
                                    $taskHasErrors = $errors->getBag($taskErrorBag)->any();
                                @endphp
                                <article class="task-card">
                                    <div class="heading-row">
                                        <div>
                                            <h4>{{ $task->taskName }}</h4>
                                            <span class="muted">Starts {{ $task->startDate->format('M j, Y') }}</span>
                                        </div>
                                        <span class="badge badge-{{ $task->status }}">{{ $task->status }}</span>
                                    </div>
                                    @if ($task->taskDesc)
                                        <p class="description" style="margin-top: 12px;">{{ $task->taskDesc }}</p>
                                    @endif
                                    <p class="hint">Created by {{ $task->creator->firstName }} {{ $task->creator->lastName }}</p>

                                    @if ($project->status === 'active')
                                        <details @if($taskHasErrors) open @endif>
                                            <summary>Edit task</summary>
                                            <form class="details-form" action="{{ route('tasks.update', $task) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="field">
                                                <label for="taskName-{{ $task->TaskID }}">Task Name</label>
                                                <input id="taskName-{{ $task->TaskID }}" name="taskName" value="{{ $taskHasErrors ? old('taskName') : $task->taskName }}" maxlength="55" required>
                                                @error('taskName', $taskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="field" style="margin-top: 12px;">
                                                <label for="taskStart-{{ $task->TaskID }}">Start Date</label>
                                                <input id="taskStart-{{ $task->TaskID }}" name="startDate" type="date" value="{{ $taskHasErrors ? old('startDate') : $task->startDate->format('Y-m-d') }}" min="{{ $progress->startDate->format('Y-m-d') }}" max="{{ $progress->finishDate->format('Y-m-d') }}" required>
                                                @error('startDate', $taskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="field" style="margin-top: 12px;">
                                                <label for="taskStatus-{{ $task->TaskID }}">Status</label>
                                                <select id="taskStatus-{{ $task->TaskID }}" name="status" required>
                                                    @foreach (['active' => 'Active', 'complete' => 'Complete', 'archive' => 'Archive'] as $value => $label)
                                                        <option value="{{ $value }}" @selected(($taskHasErrors ? old('status') : $task->status) === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @error('status', $taskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="field" style="margin-top: 12px;">
                                                <label for="taskDesc-{{ $task->TaskID }}">Description</label>
                                                <textarea id="taskDesc-{{ $task->TaskID }}" name="taskDesc" maxlength="255">{{ $taskHasErrors ? old('taskDesc') : $task->taskDesc }}</textarea>
                                                @error('taskDesc', $taskErrorBag)<p class="error">{{ $message }}</p>@enderror
                                            </div>
                                            <div class="form-actions" style="margin-top: 14px;">
                                                <button class="button" type="submit">Save Task</button>
                                            </div>
                                            </form>
                                        </details>
                                    @endif
                                </article>
                            @empty
                                <p class="empty">No tasks have been created for this progress record.</p>
                            @endforelse
                        </div>
                    </section>
                </article>
            @empty
                <p class="empty">
                    {{ Auth::user()->isAdmin() ? 'Create the first construction progress record before adding tasks.' : 'The Admin has not created a construction progress record for this project.' }}
                </p>
            @endforelse
        </div>
    </section>
</main>
</body>
</html>
