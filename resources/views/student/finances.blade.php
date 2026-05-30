@extends('layouts.student')

@section('title', 'My Finances')

@section('content')

    <style>
        :root {
            /* Map page variables to global layout theme variables */
            --text-main: var(--text-primary);
            --text-muted: var(--text-secondary);
            --text-light: var(--text-secondary);
            --bg-main: transparent;
            --border-light: var(--border-light);

            --card-bg: var(--bg-white);
            --card-border: var(--border-light);
            --table-header-bg: var(--bg-white);

            --success-bg: #dcfce7;
            --success-text: #166534;
            --success-border: #059669;

            --warning-bg: #fef9c3;
            --warning-text: #854d0e;
            --warning-border: #ca8a04;

            --danger-bg: #fee2e2;
            --danger-text: #991b1b;
            --danger-border: #dc2626;
        }

        /* Dark Mode Colors */
        [data-theme="dark"] {
            /* General Colors */
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-light: #cbd5e1;
            --bg-main: transparent;
            --border-light: #334155;

            --card-bg: #1e293b;
            --card-border: #334155;
            --table-header-bg: #0f172a;

            /* Success Theme */
            --success-bg: #064e3b;
            --success-text: #6ee7b7;
            --success-border: #059669;

            /* Warning Theme */
            --warning-bg: #713f12;
            --warning-text: #fde047;
            --warning-border: #ca8a04;

            /* Danger Theme */
            --danger-bg: #7f1d1d;
            --danger-text: #fca5a5;
            --danger-border: #dc2626;
        }

        .stat-card {
            border-radius: 12px;
            padding: 1.75rem 2rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.08);
        }
    </style>

    <div style="max-width: 1200px; margin: 0 auto;">
        <div class="page-header" style="margin-bottom: 2rem;">
            <h1 style="font-size: 2.2rem; font-weight: 900; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.02em;">My Finances</h1>
            <p style="color: var(--text-muted); font-size: 1.05rem; font-weight: 500;">View your payment history and status</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <!-- Total Paid Card (Matches Dashboard Blue) -->
            <div class="stat-card" style="background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%); color: white; border: none; box-shadow: 0 10px 25px -5px rgba(30, 58, 138, 0.3);">
                <div style="background: rgba(255,255,255,0.15); padding: 1rem; border-radius: 14px; backdrop-filter: blur(10px);">
                    <i data-lucide="check-circle" style="width: 32px; height: 32px; color: white;"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; opacity: 0.8; margin-bottom: 0.25rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Total Paid</div>
                    <div style="font-size: 1.85rem; font-weight: 800; letter-spacing: -0.01em; line-height: 1.1;">
                        ₱{{ number_format($totalPaid, 2) }}
                    </div>
                </div>
            </div>

            <!-- Pending Balance Card -->
            <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--card-border);">
                <div style="background: rgba(217, 119, 6, 0.15); color: #d97706; padding: 1rem; border-radius: 14px;">
                    <i data-lucide="clock" style="width: 32px; height: 32px;"></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Pending</div>
                    <div style="font-size: 1.85rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.01em; line-height: 1.1;">
                        ₱{{ number_format($totalPending, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div style="background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden;">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-light); display: flex; align-items: center; gap: 10px; background: rgba(0,0,0,0.01);">
                <div style="background: rgba(75, 85, 99, 0.15); color: #4b5563; padding: 8px; border-radius: 10px;">
                    <i data-lucide="history" style="width: 20px; height: 20px;"></i>
                </div>
                <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-main); margin: 0;">Payment History</h2>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: var(--table-header-bg);">
                        <tr style="border-bottom: 1px solid var(--border-light);">
                            <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Semester</th>
                            <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Academic Year</th>
                            <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Amount</th>
                            <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Status</th>
                            <th style="padding: 1.25rem 2rem; text-align: left; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr style="border-bottom: 1px solid var(--border-light); transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.01)';" onmouseout="this.style.background='transparent';">
                                <td style="padding: 1.25rem 2rem; font-weight: 700; color: var(--text-main); font-size: 0.95rem;">
                                    {{ $payment->semester }}
                                </td>
                                <td style="padding: 1.25rem 2rem; color: var(--text-muted); font-size: 0.95rem; font-weight: 500;">
                                    {{ $payment->academic_year }}
                                </td>
                                <td style="padding: 1.25rem 2rem;">
                                    <span style="font-weight: 800; color: var(--text-main); font-size: 1.05rem;">₱{{ number_format($payment->amount, 2) }}</span>
                                </td>
                                <td style="padding: 1.25rem 2rem;">
                                    @if($payment->status === 'paid')
                                        <span style="background: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border); padding: 0.4rem 0.85rem; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.05em;">
                                            <i data-lucide="check" style="width: 14px; height: 14px;"></i> Paid
                                        </span>
                                    @elseif($payment->status === 'pending')
                                        <span style="background: var(--warning-bg); color: var(--warning-text); border: 1px solid var(--warning-border); padding: 0.4rem 0.85rem; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.05em;">
                                            <i data-lucide="clock" style="width: 14px; height: 14px;"></i> Pending
                                        </span>
                                    @else
                                        <span style="background: var(--danger-bg); color: var(--danger-text); border: 1px solid var(--danger-border); padding: 0.4rem 0.85rem; border-radius: 20px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.05em;">
                                            <i data-lucide="x" style="width: 14px; height: 14px;"></i> Failed
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1.25rem 2rem; color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">
                                    {{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 5rem 2rem; text-align: center;">
                                    <div style="background: rgba(75, 85, 99, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                                        <i data-lucide="receipt" style="width: 40px; height: 40px; color: #9ca3af;"></i>
                                    </div>
                                    <h3 style="font-weight: 800; font-size: 1.25rem; color: var(--text-main); margin-bottom: 0.5rem;">No payment records</h3>
                                    <p style="color: var(--text-muted); font-size: 0.95rem;">Your payment history will appear here once processed.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection