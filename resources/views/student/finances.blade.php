@extends('layouts.student')

@section('title', 'My Finances')

@section('content')

<style>
    :root {
        /* General Colors */
        --text-main: #0f172a;
        --text-muted: #64748b;
        --text-light: #475569;
        --bg-main: transparent;
        --border-light: #f1f5f9;
        
        --card-bg: #ffffff;
        --card-border: #e2e8f0;
        --table-header-bg: #f8fafc;
        
        /* Gradient Colors (Total Paid Card) */
        --grad-start: #1e3a8a;
        --grad-end: #2563eb;
        
        /* Sky Theme (Pending Card Icon) */
        --sky-bg: #e0f2fe;
        --sky-text-dark: #0284c7;
        
        /* Success Theme (Paid Pill) */
        --success-bg: #dcfce7;
        --success-text: #166534;
        
        /* Warning Theme (Pending Pill) */
        --warning-bg: #fef3c7;
        --warning-text: #92400e;

        /* Danger Theme (Failed Pill) */
        --danger-bg: #fee2e2;
        --danger-text: #991b1b;
    }

    /* Dark Mode Colors - Sleek Slate */
    .dark, [data-theme="dark"], [data-bs-theme="dark"] {
        /* General Colors */
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --text-light: #cbd5e1;
        --bg-main: transparent;
        --border-light: #334155;
        
        --card-bg: #1e293b;
        --card-border: #334155;
        --table-header-bg: #0f172a;
        
        /* Gradient Colors */
        --grad-start: #1e40af;
        --grad-end: #3b82f6;
        
        /* Sky Theme */
        --sky-bg: #0c4a6e;
        --sky-text-dark: #bae6fd;
        
        /* Success Theme */
        --success-bg: #064e3b;
        --success-text: #6ee7b7;
        
        /* Warning Theme */
        --warning-bg: #78350f;
        --warning-text: #fde68a;

        /* Danger Theme */
        --danger-bg: #7f1d1d;
        --danger-text: #fca5a5;
    }
</style>

<div style="max-width: 1200px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">My Finances</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">View your payment history and status</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, var(--grad-start) 0%, var(--grad-end) 100%); border-radius: 16px; padding: 1.5rem; color: white;">
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

        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background: var(--sky-bg); color: var(--sky-text-dark); padding: 0.75rem; border-radius: 12px;">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Pending</div>
                    <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-main);">₱{{ number_format($totalPending, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 16px; overflow: hidden;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-light);">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main); margin: 0;">Payment History</h2>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--table-header-bg);">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Semester</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Academic Year</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Amount</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Status</th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: var(--text-muted);">Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr style="border-bottom: 1px solid var(--border-light);">
                            <td style="padding: 1rem; font-weight: 600; color: var(--text-main);">{{ $payment->semester }}</td>
                            <td style="padding: 1rem; color: var(--text-light);">{{ $payment->academic_year }}</td>
                            <td style="padding: 1rem;">
                                <span style="font-weight: 700; color: var(--text-main);">₱{{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td style="padding: 1rem;">
                                @if($payment->status === 'paid')
                                    <span style="background: var(--success-bg); color: var(--success-text); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ✓ Paid
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span style="background: var(--warning-bg); color: var(--warning-text); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ⏳ Pending
                                    </span>
                                @else
                                    <span style="background: var(--danger-bg); color: var(--danger-text); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600;">
                                        ✗ Failed
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem; color: var(--text-muted); font-size: 0.875rem;">
                                {{ $payment->paid_at ? $payment->paid_at->format('M d, Y g:i A') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 4rem 2rem; text-align: center;">
                                <svg style="width: 64px; height: 64px; color: var(--text-muted); margin: 0 auto 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <h3 style="font-weight: 600; color: var(--text-main); margin-bottom: 0.5rem;">No payment records</h3>
                                <p style="color: var(--text-muted);">Your payment history will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection