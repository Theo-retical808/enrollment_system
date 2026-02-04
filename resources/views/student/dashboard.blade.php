@extends('layouts.app')

@section('nav-links')
    <span style="color: #4f46e5;">Welcome, {{ $student->full_name }}</span>
    <form method="POST" action="{{ route('student.logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Logout</button>
    </form>
@endsection

@section('content')
<div style="padding: 2rem 0;">
    <div class="card">
        <h1 style="color: #4f46e5; margin-bottom: 1rem;">Student Dashboard</h1>
        
        <div class="grid grid-2">
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Student Information</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <p><strong>Student ID:</strong> {{ $student->student_id }}</p>
                    <p><strong>Name:</strong> {{ $student->full_name }}</p>
                    <p><strong>Email:</strong> {{ $student->email }}</p>
                    <p><strong>School:</strong> {{ $student->school->name ?? 'N/A' }}</p>
                    <p><strong>Year Level:</strong> {{ $student->year_level }}</p>
                    <p><strong>Status:</strong> 
                        <span style="color: {{ $student->isRegular() ? '#10b981' : '#f59e0b' }};">
                            {{ $student->isRegular() ? 'Regular' : 'Irregular' }}
                        </span>
                    </p>
                </div>
                
                <!-- Payment Status Section -->
                <div style="margin-top: 1.5rem;">
                    <h3 style="color: #374151; margin-bottom: 1rem;">Payment Status</h3>
                    <div style="background: {{ $paymentStatus['status'] === 'paid' ? '#f0fdf4' : '#fef2f2' }}; padding: 1rem; border-radius: 0.5rem; border: 1px solid {{ $paymentStatus['status'] === 'paid' ? '#bbf7d0' : '#fecaca' }};">
                        <p style="color: {{ $paymentStatus['status'] === 'paid' ? '#16a34a' : '#dc2626' }}; font-weight: 600;">
                            {{ $paymentStatus['message'] }}
                        </p>
                        
                        @if($paymentStatus['status'] === 'paid')
                            <p style="color: #16a34a; margin-top: 0.5rem; font-size: 0.875rem;">
                                <strong>Amount Paid:</strong> ₱{{ number_format($paymentStatus['amount_paid'], 2) }}
                            </p>
                            <p style="color: #16a34a; font-size: 0.875rem;">
                                <strong>Paid On:</strong> {{ $paymentStatus['paid_at']->format('M d, Y g:i A') }}
                            </p>
                        @else
                            @if(isset($paymentStatus['amount_due']))
                                <p style="color: #dc2626; margin-top: 0.5rem; font-size: 0.875rem;">
                                    <strong>Amount Due:</strong> ₱{{ number_format($paymentStatus['amount_due'], 2) }}
                                </p>
                            @endif
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('student.payment.required') }}" class="btn btn-primary" style="font-size: 0.875rem;">
                                    Pay Now
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div>
                <h3 style="color: #374151; margin-bottom: 1rem;">Enrollment Status</h3>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 0.5rem;">
                    <div style="margin-bottom: 1rem; padding: 1rem; background: {{ $student->isRegular() ? '#f0fdf4' : '#fef3c7' }}; border-radius: 0.5rem; border: 1px solid {{ $student->isRegular() ? '#bbf7d0' : '#fcd34d' }};">
                        <p style="color: {{ $student->isRegular() ? '#16a34a' : '#d97706' }}; font-weight: 600;">
                            {{ $classificationInfo['message'] }}
                        </p>
                        @if(!$student->isRegular() && isset($recommendedAction['retake_courses']) && count($recommendedAction['retake_courses']) > 0)
                            <p style="color: #d97706; margin-top: 0.5rem; font-size: 0.875rem;">
                                <strong>Courses to retake:</strong> {{ implode(', ', $recommendedAction['retake_courses']) }}
                            </p>
                        @endif
                    </div>
                    
                    @if($currentEnrollment)
                        <p><strong>Current Enrollment:</strong> {{ ucfirst($currentEnrollment->status) }}</p>
                        <p><strong>Semester:</strong> {{ $currentEnrollment->semester }}</p>
                        <p><strong>Academic Year:</strong> {{ $currentEnrollment->academic_year }}</p>
                        <p><strong>Total Units:</strong> {{ $currentEnrollment->total_units }}</p>
                        
                        @if($currentEnrollment->status === 'approved')
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('student.schedule') }}" class="btn btn-primary">View Full Schedule</a>
                            </div>
                        @elseif($currentEnrollment->status === 'submitted')
                            <div style="margin-top: 1rem; padding: 0.75rem; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 0.5rem;">
                                <p style="color: #d97706; font-size: 0.875rem; margin: 0;">
                                    <strong>⏳ Under Review:</strong> Your schedule is being reviewed by your professor.
                                </p>
                            </div>
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('student.schedule') }}" class="btn btn-secondary">View Submitted Schedule</a>
                            </div>
                        @elseif($currentEnrollment->status === 'draft')
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('student.schedule') }}" class="btn btn-secondary" style="margin-right: 0.5rem;">View Draft Schedule</a>
                                @if($student->isRegular())
                                    <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary">Continue Enrollment</a>
                                @else
                                    <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary">Continue Enrollment</a>
                                @endif
                            </div>
                        @endif
                    @else
                        <p>No current enrollment found.</p>
                        <div style="margin-top: 1rem;">
                            @if($canAccessEnrollment['can_access'])
                                @if($student->isRegular())
                                    <a href="{{ route('student.enrollment.regular') }}" class="btn btn-primary">Get Assigned Schedule</a>
                                @else
                                    <a href="{{ route('student.enrollment.irregular') }}" class="btn btn-primary">Select Courses</a>
                                @endif
                            @else
                                <div style="padding: 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.5rem; margin-bottom: 1rem;">
                                    <p style="color: #dc2626; font-size: 0.875rem;">
                                        <strong>Enrollment Locked:</strong> {{ $canAccessEnrollment['reason'] }}
                                    </p>
                                </div>
                                <a href="{{ route('student.payment.required') }}" class="btn btn-primary">
                                    Complete Payment to Enroll
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        @if($currentEnrollment && $currentEnrollment->courses->count() > 0)
        <div style="margin-top: 2rem;">
            <h3 style="color: #374151; margin-bottom: 1rem;">
                @if($currentEnrollment->status === 'approved')
                    Approved Schedule
                @elseif($currentEnrollment->status === 'submitted')
                    Schedule Under Review
                @else
                    Current Schedule (Draft)
                @endif
            </h3>
            <div style="background: {{ $currentEnrollment->status === 'approved' ? '#f0fdf4' : ($currentEnrollment->status === 'submitted' ? '#fef3c7' : '#f3f4f6') }}; border: 1px solid {{ $currentEnrollment->status === 'approved' ? '#bbf7d0' : ($currentEnrollment->status === 'submitted' ? '#fcd34d' : '#d1d5db') }}; padding: 1rem; border-radius: 0.5rem;">
                @if($currentEnrollment->status === 'approved')
                    <p style="color: #16a34a; margin-bottom: 1rem;">
                        <strong>✓ Your enrollment has been approved!</strong>
                    </p>
                @elseif($currentEnrollment->status === 'submitted')
                    <p style="color: #d97706; margin-bottom: 1rem;">
                        <strong>⏳ Schedule submitted for review</strong>
                    </p>
                @else
                    <p style="color: #6b7280; margin-bottom: 1rem;">
                        <strong>📝 Draft Schedule</strong> - Continue enrollment to submit for approval
                    </p>
                @endif
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #4f46e5; color: white;">
                                <th style="padding: 0.75rem; text-align: left;">Course Code</th>
                                <th style="padding: 0.75rem; text-align: left;">Course Title</th>
                                <th style="padding: 0.75rem; text-align: left;">Units</th>
                                <th style="padding: 0.75rem; text-align: left;">Schedule</th>
                                <th style="padding: 0.75rem; text-align: left;">Room</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentEnrollment->courses as $course)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 0.75rem;">{{ $course->course_code }}</td>
                                <td style="padding: 0.75rem;">{{ $course->title }}</td>
                                <td style="padding: 0.75rem;">{{ $course->units }}</td>
                                <td style="padding: 0.75rem;">
                                    @if($course->pivot->schedule_day && $course->pivot->start_time && $course->pivot->end_time)
                                        {{ $course->pivot->schedule_day }} 
                                        {{ date('g:i A', strtotime($course->pivot->start_time)) }} - 
                                        {{ date('g:i A', strtotime($course->pivot->end_time)) }}
                                    @else
                                        TBA
                                    @endif
                                </td>
                                <td style="padding: 0.75rem;">{{ $course->pivot->room ?? 'TBA' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid {{ $currentEnrollment->status === 'approved' ? '#bbf7d0' : ($currentEnrollment->status === 'submitted' ? '#fcd34d' : '#d1d5db') }};">
                    <p style="font-size: 0.875rem; color: #6b7280;">
                        <strong>Total Courses:</strong> {{ $currentEnrollment->courses->count() }} | 
                        <strong>Total Units:</strong> {{ $currentEnrollment->courses->sum('units') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection