@extends('layouts.student')

@section('title', 'My Finances')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">My Finances</h1>
        <p style="color: #64748b; font-size: 0.95rem;">View your payment history and status</p>
    </div>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 1.5rem; color: white;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Total Paid</div>
                    <div style="font-size: 1.75rem; font-weight: 800;">₱{{ number_format($totalPaid, 2) }}</div>
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
                    <div style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Pending</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a;">₱{{ number_format($totalPending, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin: 0;">Payment History</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Semester</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Academic Year</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Amount</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Status</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem; font-weight: 600; color: #0f172a;">{{ $payment->semester }}</td>
                            <td style="padding: 1rem; color: #475569;">{{ $payment->academic_year }}</td>
                            <td style="padding: 1rem;">
                                <span style="font-weight: 700; color: #0f172a;">₱{{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td style="padding: 1rem;">
                                @if($payment->status === 'paid')
                                    <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ✓ Paid
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ⏳ Pending
                                    </span>
                                @else
                                    <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ✗ Failed
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem; color: #64748b; font-size: 0.875rem;">
                                {{ $payment->paid_at ? $payment->paid_at->format('M d, Y g:i A') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 4rem 2rem; text-align: center;">
                                <svg style="width: 64px; height: 64px; color: #cbd5e1; margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 style="font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">No payment records</h3>
                                <p style="color: #64748b;">Your payment history will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
