@extends('layouts.student')

@section('title', 'Payment Required')

@section('content')
<div style="max-width: 800px; margin: 2rem auto;">
    <div class="card" style="padding: 3rem 2rem; text-align: center; border: none; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">
        <div style="width: 80px; height: 80px; background: #fff1f2; color: #e11d48; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem;">
            <i data-lucide="alert-triangle" style="width: 40px; height: 40px;"></i>
        </div>

        <h1 style="color: #111827; font-size: 2rem; font-weight: 800; margin-bottom: 1rem;">Payment Required</h1>
        <p style="color: #4b5563; font-size: 1.1rem; margin-bottom: 2.5rem; max-width: 500px; margin-left: auto; margin-right: auto;">
            You need to pay your enrollment fee before you can access the enrollment system.
        </p>

        <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 1.25rem; margin-bottom: 2.5rem; display: flex; align-items: center; gap: 12px; text-align: left;">
            <i data-lucide="info" style="color: #b91c1c; width: 20px; height: 20px;"></i>
            <span style="color: #991b1b; font-weight: 600;">{{ $paymentStatus['message'] }}</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; text-align: left; margin-bottom: 3rem; padding: 2rem; background: #f9fafb; border-radius: 16px;">
            <div>
                <h3 style="color: #111827; font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Details</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.95rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #6b7280;">Student Name:</span>
                        <span style="color: #111827; font-weight: 600;">{{ $student->full_name }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #6b7280;">Student ID:</span>
                        <span style="color: #111827; font-weight: 600;">{{ $student->student_id }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #6b7280;">Amount Due:</span>
                        <span style="color: #b91c1c; font-weight: 800; font-size: 1.1rem;">₱{{ number_format($paymentStatus['amount_due'] ?? 5000, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 style="color: #111827; font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Instructions</h3>
                <p style="color: #4b5563; font-size: 0.9rem; line-height: 1.6;">
                    Please visit the cashier's office or use the online portal to settle your balance. Office hours: <strong>{{ $paymentPortalInfo['office_hours'] }}</strong>.
                </p>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem; max-width: 400px; margin: 0 auto;">
            <a href="{{ $paymentPortalInfo['portal_url'] }}" class="btn" style="background: #2563eb; color: white; padding: 1rem; font-size: 1rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i data-lucide="external-link" style="width: 20px; height: 20px;"></i>
                Pay via Online Portal
            </a>
            
            <form method="POST" action="{{ route('student.payment.simulate') }}">
                @csrf
                <button type="submit" class="btn" style="background: white; border: 1px solid #e5e7eb; color: #4b5563; width: 100%; padding: 0.8rem; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i data-lucide="play-circle" style="width: 18px; height: 18px;"></i>
                    Simulate Payment (Demo)
                </button>
            </form>
            
            <a href="{{ route('student.dashboard') }}" style="color: #6b7280; font-size: 0.9rem; margin-top: 1rem; text-decoration: none;">
                ← Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection