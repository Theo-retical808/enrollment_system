@extends('layouts.admin')

@section('content')
<div class="page-header">
    <h1>Payment Management</h1>
    <p>View, confirm, and manage all student payments</p>
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
                    <span style="color: #64748b; font-size: 0.75rem;">{{ $payment->student->student_id ?? '' }}</span>
                </td>
                <td style="font-size: 0.8rem;">{{ ucfirst(str_replace('_', ' ', $payment->payment_type ?? 'N/A')) }}</td>
                <td><strong>₱{{ number_format($payment->amount, 2) }}</strong></td>
                <td style="font-size: 0.8rem;">{{ $payment->semester ?? 'N/A' }} {{ $payment->academic_year ?? '' }}</td>
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
                <td style="font-size: 0.8rem; color: #64748b;">{{ $payment->created_at->format('M d, Y') }}</td>
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
            <tr><td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">No payments found.</td></tr>
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
