@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1>Admin Dashboard</h1>
    <p>System overview and quick actions</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Students</div>
        <div class="stat-value">{{ $stats['total_students'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Professors</div>
        <div class="stat-value">{{ $stats['total_professors'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Enrollments</div>
        <div class="stat-value" style="color: #f59e0b;">{{ $stats['pending_enrollments'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Approved Enrollments</div>
        <div class="stat-value" style="color: #16a34a;">{{ $stats['approved_enrollments'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Payments</div>
        <div class="stat-value" style="color: #f59e0b;">{{ $stats['pending_payments'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Confirmed Payments</div>
        <div class="stat-value" style="color: #16a34a;">{{ $stats['confirmed_payments'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value" style="font-size: 1.5rem;">₱{{ number_format($stats['total_revenue'], 2) }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">
            <h2>Recent Enrollments</h2>
            <a href="{{ route('admin.enrollments') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEnrollments as $enrollment)
                <tr>
                    <td>
                        <strong>{{ $enrollment->student->full_name ?? 'N/A' }}</strong><br>
                        <span style="color: #64748b; font-size: 0.75rem;">{{ $enrollment->student->student_id ?? '' }}</span>
                    </td>
                    <td>
                        @if($enrollment->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($enrollment->status === 'submitted')
                            <span class="badge badge-warning">Pending</span>
                        @elseif($enrollment->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($enrollment->status) }}</span>
                        @endif
                    </td>
                    <td style="font-size: 0.8rem; color: #64748b;">{{ $enrollment->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; color: #94a3b8;">No enrollments yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Recent Payments</h2>
            <a href="{{ route('admin.payments') }}" class="btn btn-outline btn-sm">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPayments as $payment)
                <tr>
                    <td>
                        <strong>{{ $payment->student->full_name ?? 'N/A' }}</strong><br>
                        <span style="color: #64748b; font-size: 0.75rem;">{{ $payment->student->student_id ?? '' }}</span>
                    </td>
                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                    <td>
                        @if($payment->status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge badge-warning">Pending</span>
                        @else
                            <span class="badge badge-danger">{{ ucfirst($payment->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; color: #94a3b8;">No payments yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
