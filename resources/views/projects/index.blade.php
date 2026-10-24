<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Projects - InfraCon</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; padding: 32px 20px; font-family: Arial, sans-serif; color: #1f2937; background: #f3f4f6; }
        .page { width: min(1200px, 100%); margin: 0 auto; }
        .topbar, .actions, .form-actions { display: flex; align-items: center; gap: 12px; }
        .topbar { justify-content: space-between; margin-bottom: 24px; }
        h1, h2, h3 { margin-top: 0; }
        h1 { margin-bottom: 6px; }
        .subtitle, .muted { color: #64748b; }
        .subtitle { margin: 0; }
        .card { margin-bottom: 24px; padding: 24px; border-radius: 12px; background: #fff; box-shadow: 0 8px 24px rgba(15, 23, 42, .07); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field label, .group-label { display: block; margin-bottom: 7px; font-weight: 600; }
        input, select { width: 100%; min-height: 44px; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; background: #fff; }
        input:focus, select:focus { outline: 3px solid rgba(37, 99, 235, .16); border-color: #2563eb; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 44px; padding: 11px 17px; border: 0; border-radius: 8px; color: #fff; background: #2563eb; font: inherit; text-decoration: none; cursor: pointer; }
        .button:hover { background: #1d4ed8; }
        .button-secondary { background: #475569; }
        .button-secondary:hover { background: #334155; }
        .success { margin-bottom: 20px; padding: 13px 15px; border-radius: 8px; color: #166534; background: #dcfce7; }
        .error { margin: 7px 0 0; color: #b91c1c; font-size: 14px; }
        .hint { margin: 7px 0 0; color: #64748b; font-size: 13px; }
        .check-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .check-option { display: flex; align-items: flex-start; gap: 8px; padding: 6px; }
        .check-option input { width: 18px; min-height: 18px; margin: 1px 0 0; }
        .check-option label { margin: 0; font-weight: 400; }
        .project-list { display: grid; gap: 18px; }
        .project-card { padding: 22px; border: 1px solid #e2e8f0; border-radius: 10px; }
        .project-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
        .project-heading h3 { margin-bottom: 5px; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; text-transform: capitalize; }
        .badge-active { color: #166534; background: #dcfce7; }
        .badge-complete { color: #1e40af; background: #dbeafe; }
        .badge-cancelled { color: #991b1b; background: #fee2e2; }
        .badge-archive { color: #475569; background: #e2e8f0; }
        .details { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin: 0; }
        .details div { padding: 12px; border-radius: 8px; background: #f8fafc; }
        .details dt { margin-bottom: 4px; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .details dd { margin: 0; overflow-wrap: anywhere; }
        details { margin-top: 18px; }
        summary { color: #1d4ed8; font-weight: 700; cursor: pointer; }
        .edit-form { margin-top: 18px; padding-top: 18px; border-top: 1px solid #e2e8f0; }
        .empty { margin: 0; color: #64748b; }
        @media (max-width: 900px) {
            body { padding: 24px 16px; }
            .details { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 680px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .grid, .check-list { grid-template-columns: 1fr; }
            .project-heading { flex-direction: column; }
            .actions { width: 100%; flex-direction: column; align-items: stretch; }
            .actions > *, .actions form, .actions .button { width: 100%; }
        }
        @media (max-width: 480px) {
            body { padding: 16px 12px; }
            h1 { font-size: 26px; }
            h2 { font-size: 21px; }
            .card, .project-card { padding: 18px; }
            .details { grid-template-columns: 1fr; }
            .form-actions, .form-actions .button { width: 100%; }
        }
    </style>
</head>
<body>
<main class="page">
    <header class="topbar">
        <div>
            <h1>{{ Auth::user()->isAdmin() ? 'Manage Construction Projects' : 'Construction Projects' }}</h1>
            <p class="subtitle">
                {{ Auth::user()->isAdmin() ? 'Create project records, maintain timelines, and control Foreman and Client access.' : 'View the construction projects assigned to your account.' }}
            </p>
        </div>
        <div class="actions">
            <a class="button button-secondary" href="{{ route('home') }}">Back to Home</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="button button-secondary" type="submit">Log Out</button>
            </form>
        </div>
    </header>

    @if (session('project_management_success'))
        <div class="success" role="status">{{ session('project_management_success') }}</div>
    @endif

    @if (Auth::user()->isAdmin())
        <section class="card">
            <h2>Create Construction Project</h2>
            <form action="{{ route('admin.projects.store') }}" method="POST">
                @csrf
                <div class="grid">
                    <div class="field">
                        <label for="projectName">Project Name</label>
                        <input id="projectName" name="projectName" value="{{ old('projectName') }}" maxlength="50" required>
                        @error('projectName', 'createProject')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="location">Location</label>
                        <input id="location" name="location" value="{{ old('location') }}" maxlength="100" required>
                        @error('location', 'createProject')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="startedDate">Start Date</label>
                        <input id="startedDate" name="startedDate" type="date" value="{{ old('startedDate') }}" required>
                        @error('startedDate', 'createProject')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="targetEndDate">Target End Date</label>
                        <input id="targetEndDate" name="targetEndDate" type="date" value="{{ old('targetEndDate') }}" required>
                        @error('targetEndDate', 'createProject')<p class="error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label for="actualEndDate">Actual End Date</label>
                        <input id="actualEndDate" name="actualEndDate" type="date" value="{{ old('actualEndDate') }}">
                        @error('actualEndDate', 'createProject')<p class="error">{{ $message }}</p>@enderror
                        <p class="hint">Required only when the project is complete.</p>
                    </div>
                    <div class="field">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            @foreach (['active' => 'Active', 'complete' => 'Complete', 'cancelled' => 'Cancelled', 'archive' => 'Archive'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status', 'createProject')<p class="error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div style="margin-top: 18px;"> <!-- Select client and foreman through checkbox method to assign and select -->
                    <span class="group-label">Assign Foremen and Clients</span>
                    <div class="check-list">
                        @forelse ($assignableUsers as $assignableUser)
                            <div class="check-option">
                                <input id="create-access-{{ $assignableUser->UserID }}" name="accessUserIDs[]" type="checkbox" value="{{ $assignableUser->UserID }}" @checked(in_array($assignableUser->UserID, old('accessUserIDs', [])))>
                                <label for="create-access-{{ $assignableUser->UserID }}">
                                    {{ $assignableUser->firstName }} {{ $assignableUser->lastName }}
                                    <span class="muted">({{ $assignableUser->role->roleName }})</span>
                                </label>
                            </div>
                        @empty
                            <span class="muted">No active Foreman or Client accounts are available.</span>
                        @endforelse
                    </div>
                    @error('accessUserIDs', 'createProject')<p class="error">{{ $message }}</p>@enderror
                    @error('accessUserIDs.*', 'createProject')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-actions" style="margin-top: 20px;">
                    <button class="button" type="submit">Create Project</button>
                </div>
            </form>
        </section>
    @endif

    <section class="card">
        <h2>{{ Auth::user()->isAdmin() ? 'Project Records' : 'Assigned Projects' }}</h2>
        <div class="project-list">
            @forelse ($projects as $project)
                @php
                    $errorBag = 'manageProject'.$project->ConProjID;
                    $selectedUsers = old('accessUserIDs', $project->accessibleUsers->pluck('UserID')->all());
                @endphp
                <article class="project-card">
                    <div class="project-heading">
                        <div>
                            <h3>{{ $project->projectName }}</h3> <!-- Project name and location -->
                            <span class="muted"; style="font-weight: bold;">{{ $project->location }}</span>
                        </div>
                        <span class="badge badge-{{ $project->status }}">{{ $project->status }}</span>
                    </div>

                    <dl class="details"; style="font-weight: bold;"> <!-- Details of the construction project -->
                        <div><dt>Started</dt><dd>{{ $project->startedDate->format('M j, Y') }}</dd></div>
                        <div><dt>Target end</dt><dd>{{ $project->targetEndDate->format('M j, Y') }}</dd></div>
                        <div><dt>Actual end</dt><dd>{{ $project->actualEndDate?->format('M j, Y') ?? 'Not completed' }}</dd></div>
                        <div><dt>Created by</dt><dd>{{ $project->creator?->firstName }} {{ $project->creator?->lastName }}</dd></div>
                    </dl>

                    <p class="hint"; style="font-weight: bold;">
                        Access: {{ $project->accessibleUsers->isEmpty() ? 'No Foreman or Client assigned' : $project->accessibleUsers->map(fn ($user) => $user->firstName.' '.$user->lastName.' ('.$user->role->roleName.')')->join(', ') }}
                    </p>

                    @if (Auth::user()->isAdmin())
                        <details @if($errors->$errorBag->any()) open @endif>
                            <summary>Edit project</summary>
                            <form class="edit-form" action="{{ route('admin.projects.update', $project) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="grid">
                                    <div class="field">
                                        <label for="projectName-{{ $project->ConProjID }}">Project Name</label>
                                        <input id="projectName-{{ $project->ConProjID }}" name="projectName" value="{{ old('projectName', $project->projectName) }}" maxlength="50" required>
                                        @error('projectName', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="location-{{ $project->ConProjID }}">Location</label>
                                        <input id="location-{{ $project->ConProjID }}" name="location" value="{{ old('location', $project->location) }}" maxlength="100" required>
                                        @error('location', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="startedDate-{{ $project->ConProjID }}">Start Date</label>
                                        <input id="startedDate-{{ $project->ConProjID }}" name="startedDate" type="date" value="{{ old('startedDate', $project->startedDate->format('Y-m-d')) }}" required>
                                        @error('startedDate', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="targetEndDate-{{ $project->ConProjID }}">Target End Date</label>
                                        <input id="targetEndDate-{{ $project->ConProjID }}" name="targetEndDate" type="date" value="{{ old('targetEndDate', $project->targetEndDate->format('Y-m-d')) }}" required>
                                        @error('targetEndDate', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="actualEndDate-{{ $project->ConProjID }}">Actual End Date</label>
                                        <input id="actualEndDate-{{ $project->ConProjID }}" name="actualEndDate" type="date" value="{{ old('actualEndDate', $project->actualEndDate?->format('Y-m-d')) }}">
                                        @error('actualEndDate', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="field">
                                        <label for="status-{{ $project->ConProjID }}">Status</label>
                                        <select id="status-{{ $project->ConProjID }}" name="status" required>
                                            @foreach (['active' => 'Active', 'complete' => 'Complete', 'cancelled' => 'Cancelled', 'archive' => 'Archive'] as $value => $label)
                                                <option value="{{ $value }}" @selected(old('status', $project->status) === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('status', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                    </div>
                                </div>

                                <div style="margin-top: 18px;">
                                    <span class="group-label">Assign Foremen and Clients</span>
                                    <div class="check-list">
                                        @forelse ($assignableUsers as $assignableUser)
                                            <div class="check-option">
                                                <input id="access-{{ $project->ConProjID }}-{{ $assignableUser->UserID }}" name="accessUserIDs[]" type="checkbox" value="{{ $assignableUser->UserID }}" @checked(in_array($assignableUser->UserID, $selectedUsers))>
                                                <label for="access-{{ $project->ConProjID }}-{{ $assignableUser->UserID }}">
                                                    {{ $assignableUser->firstName }} {{ $assignableUser->lastName }}
                                                    <span class="muted">({{ $assignableUser->role->roleName }})</span>
                                                </label>
                                            </div>
                                        @empty
                                            <span class="muted">No active Foreman or Client accounts are available.</span>
                                        @endforelse
                                    </div>
                                    @error('accessUserIDs', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                    @error('accessUserIDs.*', $errorBag)<p class="error">{{ $message }}</p>@enderror
                                </div>

                                <div class="form-actions" style="margin-top: 20px;">
                                    <button class="button" type="submit">Save Project</button>
                                </div>
                            </form>
                        </details>
                    @endif
                </article>
            @empty
                <p class="empty">{{ Auth::user()->isAdmin() ? 'No construction projects have been created.' : 'No construction projects are assigned to your account.' }}</p>
            @endforelse
        </div>
    </section>
</main>
</body>
</html>
