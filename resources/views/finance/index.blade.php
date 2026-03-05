@extends('layouts.app')

@section('title', 'Finance Management')

@section('content')
<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 2rem;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Finance Management</h1>
        <p style="color: #64748b; font-size: 1rem;">Track and manage student payments</p>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 1.5rem; color: white;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Total Collected</div>
                    <div style="font-size: 1.75rem; font-weight: 800;">₱{{ number_format($stats['total_collected'], 2) }}</div>
                </div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #fef3c7; color: #f59e0b; padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Pending Payments</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a;">{{ $stats['pending_payments'] }}</div>
                </div>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: #dbeafe; color: #2563eb; padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Total Transactions</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a;">{{ $stats['total_payments'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
        <form method="GET" action="{{ route('finance.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Search Student</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Student ID or name..." 
                    style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
            </div>
            
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Status</label>
                <select name="status" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">School</label>
                <select name="school_id" style="width: 100%; padding: 0.625rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem;">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="flex: 1; background: #2563eb; color: white; padding: 0.625rem 1.25rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Filter
                </button>
                <a href="{{ route('finance.index') }}" style="flex: 1; background: #f1f5f9; color: #475569; padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Student</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">School</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Semester</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Amount</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Status</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem;">
                                <div style="font-weight: 600; color: #0f172a;">{{ $payment->student->full_name }}</div>
                                <div style="font-size: 0.875rem; color: #64748b;">{{ $payment->student->student_id }}</div>
                            </td>
                            <td style="padding: 1rem; color: #475569;">{{ $payment->student->school->name }}</td>
                            <td style="padding: 1rem; color: #475569;">{{ $payment->semester }} {{ $payment->academic_year }}</td>
                            <td style="padding: 1rem;">
                                <span style="font-weight: 700; color: #0f172a;">₱{{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td style="padding: 1rem;">
                                @if($payment->status === 'paid')
                                    <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">Paid</span>
                                @elseif($payment->status === 'pending')
                                    <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">Pending</span>
                                @else
                                    <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">Failed</span>
                                @endif
                            </td>
                            <td style="padding: 1rem; color: #64748b; font-size: 0.875rem;">{{ $payment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 4rem 2rem; text-align: center;">
                                <svg style="width: 64px; height: 64px; color: #cbd5e1; margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 style="font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">No payments found</h3>
                                <p style="color: #64748b;">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($payments->hasPages())
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
