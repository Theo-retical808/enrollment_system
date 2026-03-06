@extends('layouts.app')

@section('title', $course->course_code . ' - ' . $course->title)

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 2rem;">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('courses.index') }}" style="color: #2563eb; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Courses
        </a>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 2rem; color: white;">
            <div style="display: flex; justify-content: between; align-items: start; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;">{{ $course->course_code }}</h1>
                    <h2 style="font-size: 1.25rem; font-weight: 600; opacity: 0.9;">{{ $course->title }}</h2>
                </div>
                <span style="background: {{ $course->is_active ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.2)' }}; padding: 0.5rem 1rem; border-radius: 12px; font-weight: 600;">
                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div style="padding: 2rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px;">
                    <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">Units</div>
                    <div style="font-size: 2rem; font-weight: 800; color: #0f172a;">{{ $course->units }}</div>
                </div>
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px;">
                    <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">Year Level</div>
                    <div style="font-size: 2rem; font-weight: 800; color: #0f172a;">{{ $course->year_level }}</div>
                </div>
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px;">
                    <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">School</div>
                    <div style="font-size: 1.125rem; font-weight: 700; color: #0f172a;">{{ $course->school->code }}</div>
                </div>
                <div style="background: #f8fafc; padding: 1.5rem; border-radius: 12px;">
                    <div style="color: #64748b; font-size: 0.875rem; margin-bottom: 0.5rem;">Enrolled</div>
                    <div style="font-size: 2rem; font-weight: 800; color: #0f172a;">{{ $enrolledStudents->count() }}</div>
                </div>
            </div>

            @if($course->description)
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Description</h3>
                    <p style="color: #475569; line-height: 1.75;">{{ $course->description }}</p>
                </div>
            @endif

            @if($course->prerequisites->count() > 0)
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Prerequisites</h3>
                    <div style="display: flex; flex-wrap: gap; gap: 0.75rem;">
                        @foreach($course->prerequisites as $prereq)
                            <a href="{{ route('courses.show', $prereq->id) }}" style="background: #eff6ff; color: #2563eb; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
                                {{ $prereq->course_code }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($enrolledStudents->count() > 0)
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">Enrolled Students</h3>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f8fafc;">
                                <tr>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Student ID</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Name</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">School</th>
                                    <th style="padding: 0.75rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #64748b;">Year Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrolledStudents as $student)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 0.75rem; font-weight: 600; color: #0f172a;">{{ $student->student_id }}</td>
                                        <td style="padding: 0.75rem; color: #475569;">{{ $student->full_name }}</td>
                                        <td style="padding: 0.75rem; color: #475569;">{{ $student->school->name }}</td>
                                        <td style="padding: 0.75rem; color: #475569;">Year {{ $student->year_level }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
