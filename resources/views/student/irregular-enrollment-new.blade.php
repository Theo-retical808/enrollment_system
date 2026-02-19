@extends('layouts.student')

@section('title', 'Irregular Enrollment')

@section('styles')
<style>
    .tabs {
        display: flex;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .tab-button {
        padding: 1rem 2rem;
        border: none;
        background: none;
        color: #64748b;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s;
    }

    .tab-button.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: white;
        padding: 2rem;
        border-radius: 0.75rem;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .schedule-option {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .schedule-option:hover {
        border-color: #2563eb;
        background: #f8fafc;
    }

    .progress-bar {
        height: 8px;
        background: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
        margin: 0.5rem 0;
    }

    .progress-fill {
        height: 100%;
        background: #2563eb;
        transition: width 0.3s, background 0.3s;
    }

    .progress-fill.warning {
        background: #f59e0b;
    }

    .progress-fill.danger {
        background: #ef4444;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Irregular Student Enrollment</h1>
    <p class="page-subtitle">Select your courses manually and build your custom schedule</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="grid grid-2">
    <div class="card">
        <h2 class="card-title">Enrollment Information</h2>
        <table>
            <tbody>
                <tr>
                    <td style="font-weight: 500;">Student</td>
                    <td>{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Student ID</td>
                    <td>{{ $student->student_id }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">School</td>
                    <td>{{ $student->school->name }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 500;">Semester</td>
                    <td>{{ $enrollment->semester }} {{ $enrollment->academic_year }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2 class="card-title">Schedule Validation</h2>
        <div>
            <strong>Total Units:</strong> <span id="current-units">{{ $enrollment->total_units }}</span> / 21
            <div class="progress-bar">
                <div id="unit-progress" class="progress-fill" style="width: {{ ($enrollment->total_units / 21) * 100 }}%"></div>
            </div>
            <small style="color: #64748b;"><span id="remaining-units">{{ 21 - $enrollment->total_units }}</span> units remaining</small>
        </div>
        
        <div id="validation-status" style="margin-top: 1rem;">
            <div id="validation-errors" class="alert alert-error" style="display: none;">
                <strong>⚠️ Validation Errors:</strong>
                <ul id="error-list" style="margin-left: 1rem; margin-top: 0.5rem;"></ul>
            </div>
            
            <div id="validation-warnings" class="alert alert-warning" style="display: none;">
                <strong>⚠️ Warnings:</strong>
                <ul id="warning-list" style="margin-left: 1rem; margin-top: 0.5rem;"></ul>
            </div>
            
            <div id="validation-success" class="alert alert-success" style="display: none;">
                ✓ Schedule is valid for submission
            </div>
        </div>
    </div>
</div>

<div class="card