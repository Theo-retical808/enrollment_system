@extends('layouts.app')

@section('nav-links')
    <span style="color: #4f46e5;">{{ $student->full_name }}</span>
    <form method="POST" action="{{ route('student.logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Logout</button>
    </form>
@endsection

@section('content')
<div style="padding: 2rem 0;">
    <div class="card">
        <div class="text-center mb-4">
            <div style="font-size: 4rem; color: #f59e0b; margin-bottom: 1rem;">⚠️</div>
            <h1 style="color: #dc2626; margin-bottom: 1rem;">Payment Required</h1>
            <p style="color: #6b7280; font-size: 1.125rem;">
                You need to pay your enrollment fee before you can access the enrollment system.
            </p>
        </div>

        <div class="alert alert-error">
            <strong>{{ $paymentStatus['message'] }}</strong>
        </div>

        <div class="grid grid-2">
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Payment Information</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <p><strong>Student:</strong> {{ $student->full_name }}</p>
                    <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                    <p><strong>Semester:</strong> {{ app(\App\Services\PaymentVerificationService::class)->getCurrentSemester() }}</p>
                    <p><strong>Academic Year:</strong> {{ app(\App\Services\PaymentVerificationService::class)->getCurrentAcademicYear() }}</p>
                    
                    @if(isset($paymentStatus['amount_due']))
                        <p><strong>Amount Due:</strong> ₱{{ number_format($paymentStatus['amount_due'], 2) }}</p>
                    @endif
                    
                    @if(isset($paymentStatus['amount_paid']))
                        <p><strong>Amount Paid:</strong> ₱{{ number_format($paymentStatus['amount_paid'], 2) }}</p>
                        <p><strong>Paid At:</strong> {{ $paymentStatus['paid_at']->format('M d, Y g:i A') }}</p>
                    @endif
                </div>
            </div>
            
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Payment Options</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <p><strong>Office Hours:</strong> {{ $paymentPortalInfo['office_hours'] }}</p>
                    <p><strong>Contact:</strong> {{ $paymentPortalInfo['contact_number'] }}</p>
                    <p style="margin-top: 1rem;">{{ $paymentPortalInfo['instructions'] }}</p>
                    
                    <div style="margin-top: 1.5rem;">
                        <a href="{{ $paymentPortalInfo['portal_url'] }}" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem;">
                            Pay Online
                        </a>
                        
                        <!-- Demo payment button -->
                        <form method="POST" action="{{ route('student.payment.simulate') }}" style="margin-top: 0.5rem;">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="width: 100%;">
                                Simulate Payment (Demo)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 2rem; text-align: center;">
            <p style="color: #6b7280;">
                After making your payment, please allow 24-48 hours for verification.
                <br>
                <a href="{{ route('student.dashboard') }}" style="color: #4f46e5;">Return to Dashboard</a>
            </p>
        </div>
    </div>
</div>
@endsection