@extends('layouts.admin')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1>Enrollment Assistants</h1>
        <p style="color: var(--admin-text-secondary); font-size: 0.85rem;">Designate professors to assist in the enrollment review process</p>
    </div>
    <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">← Back to Assignments</a>
</div>

<!-- Current Enrollment Assistants -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2>Designated Enrollment Assistants ({{ $assistants->count() }})</h2>
    </div>
    @if($assistants->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Professor ID</th>
                <th>Name</th>
                <th>School</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assistants as $assistant)
            <tr>
                <td><code class="id-badge id-badge-professor">{{ $assistant->professor_id }}</code></td>
                <td><strong>{{ $assistant->full_name }}</strong></td>
                <td>{{ $assistant->school->name ?? 'N/A' }}</td>
                <td><span class="badge badge-success">Active Assistant</span></td>
                <td>
                    <form method="POST" action="{{ route('admin.enrollment-assistants.toggle', $assistant) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove {{ $assistant->full_name }} as enrollment assistant?');">
                            Remove
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">
        <p>No professors are currently designated as enrollment assistants.</p>
    </div>
    @endif
</div>

<!-- Available Professors -->
<div class="card">
    <div class="card-header">
        <h2>Available Professors ({{ $availableProfessors->count() }})</h2>
    </div>
    @if($availableProfessors->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Professor ID</th>
                <th>Name</th>
                <th>School</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($availableProfessors as $professor)
            <tr>
                <td><code class="id-badge id-badge-professor">{{ $professor->professor_id }}</code></td>
                <td><strong>{{ $professor->full_name }}</strong></td>
                <td>{{ $professor->school->name ?? 'N/A' }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.enrollment-assistants.toggle', $professor) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            Designate as Assistant
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 2rem; color: var(--admin-text-secondary);">
        <p>All active professors are already designated as enrollment assistants.</p>
    </div>
    @endif
</div>
@endsection
