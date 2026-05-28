@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1>Payment Management</h1>
    <p>View, confirm, and manage all student payments</p>
</div>

<!-- Manual Payment Form -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2>Add Payment Manually</h2>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <form method="POST" action="{{ route('admin.payments.store') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="flex: 1; min-width: 160px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Student No. *</label>
                <input type="text" name="student_id" value="{{ old('student_id') }}" required placeholder="e.g., 2024-001" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
            </div>
            <div style="min-width: 140px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Amount (₱) *</label>
                <input type="number" name="amount" value="{{ old('amount') }}" required min="0.01" step="0.01" placeholder="0.00" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
            </div>
            <div style="min-width: 180px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Payment For *</label>
                <select name="payment_type" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                    <option value="enrollment_fee" {{ old('payment_type') === 'enrollment_fee' ? 'selected' : '' }}>Enrollment</option>
                    <option value="tuition" {{ old('payment_type') === 'tuition' ? 'selected' : '' }}>Tuition</option>
                    <option value="miscellaneous" {{ old('payment_type') === 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                </select>
            </div>
            <div style="min-width: 160px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; color: var(--admin-text-primary);">Semester *</label>
                <select name="semester" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--admin-border); background: var(--admin-input-bg); color: var(--admin-text-primary); font-size: 0.85rem;">
                    <option value="1st Semester" {{ old('semester', '1st Semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd Semester" {{ old('semester') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    <option value="Summer" {{ old('semester') === 'Summer' ? 'selected' : '' }}>Summer</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Record this payment?')">Record Payment</button>
        </form>
        <p style="font-size: 0.75rem; color: var(--admin-text-secondary); margin-top: 0.5rem;">Payment will be recorded as paid with the current date and time.</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Collected</div>
        <div class="stat-value" style="font-size: 1.5rem;">₱{{ number_format($stats['total_collected'], 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Payments</div>
        <div class="stat-value" style="color: #f59e0b;">{{ $stats['pending_payments'] }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Transactions</div>
        <div class="stat-value">{{ $stats['total_payments'] }}</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('admin.payments') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; width: 100%;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by student name or ID..." style="flex: 1; min-width: 200px;">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td>
                    <strong>{{ $payment->student->full_name ?? 'N/A' }}</strong><br>
                    <span style="color: var(--admin-text-secondary); font-size: 0.75rem;">{{ $payment->student->student_id ?? '' }}</span>
                </td>
                <td style="font-size: 0.8rem;">{{ ucfirst(str_replace('_', ' ', $payment->payment_type ?? 'N/A')) }}</td>
                <td><strong>₱{{ number_format($payment->amount, 2) }}</strong></td>
                <td style="font-size: 0.8rem; color: var(--admin-text-secondary);">{{ $payment->semester ?? 'N/A' }} {{ $payment->academic_year ?? '' }}</td>
                <td>
                    @if($payment->status === 'paid')
                        <span class="badge badge-success">Paid</span>
                    @elseif($payment->status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @elseif($payment->status === 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @else
                        <span class="badge badge-secondary">{{ ucfirst($payment->status) }}</span>
                    @endif
                </td>
                <td style="font-size: 0.8rem; color: var(--admin-text-secondary);">
                    @if($payment->paid_at)
                        {{ $payment->paid_at->format('M d, Y h:i A') }}
                    @else
                        {{ $payment->created_at->format('M d, Y') }}
                    @endif
                </td>
                <td>
                    @if($payment->status === 'pending')
                        <div style="display: flex; gap: 0.25rem;">
                            <form method="POST" action="{{ route('admin.payments.confirm', $payment->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Confirm this payment?')">Confirm</button>
                            </form>
                            <form method="POST" action="{{ route('admin.payments.reject', $payment->id) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Reject this payment?')">Reject</button>
                            </form>
                        </div>
                    @else
                        <span style="color: #94a3b8; font-size: 0.8rem;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--admin-text-muted);">No payments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($payments->hasPages())
<div class="pagination">
    {{ $payments->appends(request()->query())->links('pagination::simple-default') }}
</div>
@endif
@endsection
