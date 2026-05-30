@extends('layouts.student')

@section('title', 'Payment Required')

@section('content')
<div style="min-height: calc(100vh - 120px); display: flex; align-items: center; justify-content: center; padding: 1rem 0;">
    <div style="max-width: 800px; width: 100%;">
        <div class="card" style="padding: 2.5rem; text-align: center; border: none; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
            <div style="width: 64px; height: 64px; background: #fff1f2; color: #e11d48; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                <i data-lucide="alert-triangle" style="width: 32px; height: 32px;"></i>
            </div>

            <h1 style="color: #111827; font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;">Payment Required</h1>
            <p style="color: #4b5563; font-size: 1rem; margin-bottom: 1.5rem; max-width: 500px; margin-left: auto; margin-right: auto;">
                You need to pay your enrollment fee before you can access the enrollment system.
            </p>

            <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; text-align: left; max-width: 600px; margin-left: auto; margin-right: auto;">
                <i data-lucide="info" style="color: #b91c1c; width: 20px; height: 20px; flex-shrink: 0;"></i>
                <span style="color: #991b1b; font-weight: 600; font-size: 0.95rem;">{{ $paymentStatus['message'] }}</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; text-align: left; margin-bottom: 2rem; padding: 1.5rem 2rem; background: #f9fafb; border-radius: 16px; max-width: 700px; margin-left: auto; margin-right: auto;">
                <div>
                    <h3 style="color: #111827; font-size: 0.9rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Details</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.95rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #6b7280;">Student Name:</span>
                            <span style="color: #111827; font-weight: 600;">{{ $student->full_name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #6b7280;">Student ID:</span>
                            <span style="color: #111827; font-weight: 600;">{{ $student->student_id }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                            <span style="color: #6b7280;">Amount Due:</span>
                            <span style="color: #b91c1c; font-weight: 800; font-size: 1.1rem;">₱{{ number_format($paymentStatus['amount_due'] ?? 5000, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 style="color: #111827; font-size: 0.9rem; font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;">Instructions</h3>
                    <p style="color: #4b5563; font-size: 0.95rem; line-height: 1.5; margin: 0;">
                        Please visit the cashier's office or use the online portal to settle your balance. Office hours: <strong>{{ $paymentPortalInfo['office_hours'] }}</strong>.
                    </p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem; max-width: 400px; margin: 0 auto;">
                <a href="{{ $paymentPortalInfo['portal_url'] }}" class="btn" style="background: #2563eb; color: white; padding: 0.85rem; font-size: 1rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i data-lucide="external-link" style="width: 20px; height: 20px;"></i>
                    Pay via Online Portal
                </a>
                
                <p style="color: #6b7280; font-size: 0.85rem; text-align: center; margin-top: 0.5rem; line-height: 1.4;">
                    Please visit the admin/cashier's office to process your payment. Once confirmed by the admin, you will be able to proceed with enrollment.
                </p>
                
                <a href="{{ route('student.dashboard') }}" style="color: #6b7280; font-size: 0.95rem; margin-top: 0.5rem; text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection